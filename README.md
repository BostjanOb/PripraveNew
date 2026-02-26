# Priprave.net

## TODO:
- [ ] In login, add option for remember me

## Runing MeiliSerach

```
docker compose -f docker/compose.yml up -d
php artisan scout:sync-index-settings
php artisan scout:import "App\Models\Document"
```
