import './bootstrap';
import '@tailwindplus/elements';
import.meta.glob('../images/**/*.{png,jpg,jpeg,webp,svg,gif,ico}', {
    eager: true,
    query: '?url',
    import: 'default'
});
