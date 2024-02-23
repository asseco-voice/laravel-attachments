<p align="center"><a href="https://see.asseco.com" target="_blank"><img src="https://github.com/asseco-voice/art/blob/main/evil_logo.png" width="500"></a></p>

# Attachments

Purpose of this repository is to enable attachment upload and connecting it
to any Laravel model as an M:M relation.

## Installation

Require the package with ``composer require asseco-voice/laravel-attachments``.
Service provider will be registered automatically.

## Setup

In order to use the package, migrate the tables with ``artisan migrate``
and add `Attachable` trait to model you'd like to have attachment support on.

```php
use Asseco\Attachments\App\Traits\Attachable;

class Product extends Model
{
    use Attachable;
    
    // ...   
}
```

Standard CRUD endpoints are exposed for attachment administration where you can,
among others, store an attachment. Due to the fact that attachments are a morph
relation, you have to provide your own controllers for attaching/detaching those
attachments to attachable models.

Example:

```php
Route::post('models/{model}/attachments', [ModelAttachmentController::class, 'store']);

public function store(Request $request, Model $model): JsonResponse
{
    $ids = Arr::get($request->validated(), 'attachment_ids', []);

    $model->attachments()->sync($ids);

    return response()->json('success');
}
```

# Cached attachments

Option to keep attachments in filesystem, or cache.
Based on value ATTACHMENTS_CACHE_TYPE, there are two scenarios:
- FILE
  - attachments are stored in filesystem (ATTACHMENTS_CACHE_LOCATION)
  - mapping is saved in cache key ATTACHMENTS_CACHE_MAP_KEY 
    - [attachment1->id => path1, attachment2->id => path2, ...]
- CACHE
  - attachment content is stored in cache, 1 attachment = 1 cache key
  - cache key is named as: ATTACHMENTS_CACHE_KEY_PREFIX + attachment_id

ENV variables which controls the behaviour:
 - ATTACHMENTS_CACHE_ENABLED=true   (default: false)
 - ATTACHMENTS_CACHE_MAP_KEY="ASEE_ATTACHMENTS_MAP"  (default: ASEE_ATTACHMENTS_MAP)
 - ATTACHMENTS_CACHE_TYPE="FILE"  (default: FILE)
 - ATTACHMENTS_CACHE_LOCATION="/tmp/"   (default: sys_get_temp_dir())
 - ATTACHMENTS_CACHE_KEY_PREFIX="ASEE_ATTACHMENT_"   (default: ASEE_ATTACHMENT_)
 - ATTACHMENTS_CACHE_TIME=3600   (default: 3600 seconds)

# Extending the package

Publishing the configuration will enable you to change package models as
well as controlling how migrations behave. If extending the model, make sure
you're extending the original model in your implementation.
