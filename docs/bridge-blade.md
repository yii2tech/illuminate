Blade Templates
===============

This package does not provide separated view renderer for the Blade templates as someone may expect. The reason behind
this is simple: it is redundant. There is nothing preventing you to use blade view files inside your Yii application, if
necessary. You can use global `view()` helper function to render a Blade template inside Yii controller. For example:

```php
<?php

use yii\web\Controller;

class SiteController extends Controller
{
    public function actionAbout()
    {
        return view('pages.about'); // renders file 'resources/views/pages/about.blade.php'
    }
}
```

Same helper might be used inside Yii view files for particular HTML fragments rendering. For example:


```php
<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'About';
$this->params['breadcrumbs'][] = 'About';
?>
<div class="sidebar">
    <?php /* renders file 'resources/views/components/sidebar.blade.php': */ ?>
    <?= view('components.sidebar') ?>
</div>
<div class="content">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the content page...</p>
</div>
```
