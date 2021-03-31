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
use Asseco\Attachments\Contracts\Attachable;

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
