<?php

use Property\Models\BaseAcl;

if (!empty($_SESSION['acl']['group'])) {
    
    $acl = BaseAcl::find(["conditions" => "active = 'Y' AND usergroup like '%,".$_SESSION['acl']['group'].",%'"]);
//echo '<pre>'; var_dump($acl); echo '</pre>'; die();
    $router->add(
        '/logout',
        [
            "controller" => 'login',
            "action"     => 'logout',
        ]
    );

    $router->notFound(
        [
            "controller" => "index",
            "action"     => "error403",
        ]
    );

} else {
    $acl = BaseAcl::find(["conditions" => "active = 'Y' AND usergroup like '%,999,%'"]);

    $router->add(
        '/login',
        [
            "controller" => 'login',
            "action"     => 'login',
        ]
    );

    $router->add(
        '/login/error/:params',
        [
            "controller" => 'login',
            "action"     => 'login',
            "params"     => 1
        ]
    );

    $router->add(
        '/login/process',
        [
            "controller" => 'login',
            "action"     => 'process',
        ]
    );

    $router->add(
        '/login/forgotPassword',
        [
            "controller" => 'login',
            "action"     => 'forgotPassword',
        ]
    );   
    $router->add(
        '/login/forgot/:params',
        [
            "controller" => 'login',
            "action"     => 'forgotPassword',
            "params"     => 1
        ]
    ); 
    $router->add(
        '/login/postForgotPassword',
        [
            "controller" => 'login',
            "action"     => 'postForgotPassword',
        ]
    );  

    //Reset Password
    $router->add(
        '/reset-password/{code}/{email}', 
        [
            'controller' => 'login',
            'action' => 'resetPassword'
        ]
    );

    //Change Password
    $router->add('/login/changePassword', [
        'controller' => 'login',
        'action' => 'changePassword'
    ]);

    $router->add(
        '/login/change/:params',
        [
            "controller" => 'login',
            "action"     => 'changePassword',
            "params"     => 1
        ]
    ); 
    
    //Confirm Email
    $router->add('/confirm/{code}/{email}', [
        'controller' => 'login',
        'action' => 'confirmEmail'
    ]);


    //Change Password
    $router->add('/index', [
        'controller' => 'index',
        'action' => 'index'
    ]);

    $router->setDefaults(
        [
            "controller" => "index",
            "action"     => "index",
        ]
    );

}

foreach ($acl as $key => $value) {
    if (!empty($value->url)) {
        $router->add(
            $value->url,
            [
                "controller" => $value->controller,
                "action"     => $value->action,
                "params"     => 1
            ]
        );
    }
}
$router->handle();
