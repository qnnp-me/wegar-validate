# Wegar Validate

> 通过注解来限制路由的请求方式，如未设置则自由访问； \
> 如设置 app.force = true 则强制所有路由使用注解才能被访问
> 如设置 app.throw = false 则响应的的是 404

## 基本使用

### Example

```php
<?php

use Wegar\Validate\Annotation\Method\GET;

class ExampleController {
    /**
     * POST /example/index => Method not allowed or 404
     * GET /example/index => Hello World!
     */
    #[GET]
    function index(){
        return response('Hello World!');        
    }
    /**
     * POST /example/page => Hello Page!
     * GET /example/page => Hello Page!
     * ...
     * 
     * if app.force = true
     * POST /example/page => Method not allowed
     * GET /example/page => Method not allowed
     * ...
     * 
     * if app.force = true and app.throw=false
     * POST /example/page => 404
     * GET /example/page => 404
     * ...
     */
    function page(){
        return response('Hello Page!');
    }
}
```

## 输入校验

> `POST`/`PUT`/`PATCH`方法`body`优先级高于`query`，其他方法`query`优先级高于`body`

### Example

```php
<?php

use Wegar\Validate\Annotation\Method\GET;

class ExampleController {
    #[GET(
      'field' => 'required:msg->Field is required',
      'field2' => 'max:10',
    )]
    function index(){
        return response('Hello World!');        
    }
}
```

