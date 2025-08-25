<?php


namespace App\Support;

class Html
{
    /**
     * Nimmt HTML-String ODER Tiptap/ProseMirror-Array entgegen
     * und gibt bereinigtes HTML zurück.
     *
     * @param  string|array|null  $html
     */
    public static function normalizeRte(string|array|null $html): ?string
    {
        if (!$html) return $html;

        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // Body-Wrapper, damit wir ein Fragment parsen können
        $doc->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        // 1) <p><H|UL|OL|TABLE|BLOCKQUOTE|PRE>…</…></p> -> ohne <p>
        $blockTags = ['H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'UL', 'OL', 'TABLE', 'BLOCKQUOTE', 'PRE'];
        foreach (iterator_to_array($xpath->query('//p')) as $p) {
            if ($p->childNodes->length === 1) {
                $child = $p->firstChild;
                if ($child instanceof \DOMElement && in_array(strtoupper($child->nodeName), $blockTags, true)) {
                    $p->parentNode->replaceChild($child, $p);
                }
            }
        }

        // 2) Verwaiste <li> zu <ul> gruppieren
        //    Sammle alle LI ohne UL/OL-Ahnen und wickle zusammenhängende Runs in ein UL
        $lis = iterator_to_array($xpath->query('//li[not(ancestor::ul) and not(ancestor::ol)]'));
        foreach ($lis as $li) {
            if (!$li->parentNode) continue;
            $parent = $li->parentNode;

            $ul = $doc->createElement('ul');
            // Sammle fortlaufende LI-Geschwister
            $cursor = $li;
            while ($cursor && $cursor->nodeName === 'li') {
                $next = $cursor->nextSibling;
                $ul->appendChild($cursor);
                $cursor = ($next && $next->nodeName === 'li') ? $next : null;
            }
            $parent->insertBefore($ul, $ul->firstChild); // Platz egal, da wir die LI bereits hineingezogen haben
        }

        // 3) Leere <p> entfernen
        foreach (iterator_to_array($xpath->query('//p[normalize-space()=""]')) as $empty) {
            $empty->parentNode?->removeChild($empty);
        }

        // Fragment zurückgeben (alles, was im <body> liegt)
        $out = '';
        foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return $out;
    }
}
