<?php

namespace App\Http\Livewire;

use App\Models\SeoData;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SeoManager extends Component
{
    #[Locked] public string $modelType;  // z. B. App\Models\Show
    #[Locked] public int $modelId;

    public ?SeoData $seo = null;
    public string $title = '';
    public string $description = '';
    public string $keywords = '';
    public string $schema_markup = '';

    public function mount(string $modelType, int $modelId): void
    {
        abort_unless(class_exists($modelType), 404);
        /** @var Model $model */
        $model = $modelType::findOrFail($modelId);

        $this->seo = $model->seo()->first();
        if ($this->seo) {
            $this->title = (string) $this->seo->title;
            $this->description = (string) $this->seo->description;
            $this->keywords = (string) $this->seo->keywords;
            $this->schema_markup = json_encode($this->seo->schema_markup ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        }
    }

    public function save(): void
    {
        /** @var Model $model */
        $model = ($this->modelType)::findOrFail($this->modelId);

        $payload = [
            'title' => $this->title ?: null,
            'description' => $this->description ?: null,
            'keywords' => $this->keywords ?: null,
            'schema_markup' => $this->decodeSchema($this->schema_markup),
        ];

        $seo = $model->seo()->updateOrCreate([], $payload);
        $this->seo = $seo;

        $this->dispatch('notify', message: 'SEO gespeichert.');
    }

    protected function decodeSchema(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') return null;
        try { $json = json_decode($raw, true, flags: JSON_THROW_ON_ERROR); }
        catch (\Throwable $e) { $json = null; }
        return is_array($json) ? $json : null;
    }

    public function render()
    {
        return view('livewire.seo-manager');
    }
}
