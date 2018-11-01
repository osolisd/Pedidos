<?php

use App\Util\Helpers;

$data = Helpers::getSessionObject();
?>
<!-- BEGIN PAGE HEADER-->
<!-- BEGIN PAGE TITLE-->
<div class="page-bar">
    <h1 class="page-title font-marca uppercase">{{$title}}</h1>
    @if(App\Util\Helpers::validEmptyVar(App\Util\Helpers::getCurrentCampaign()))
    <div class="page-toolbar" 
         style="display: <?=(Route::currentRouteNamed('Zonas') && App\Util\Helpers::isAdministrator()) ? 'none' : 'inline' ?>">
        <span class="badge pull-right bgmarca" style="font-size: 20px !important; height: 26px;">
            CAMPAÑA:
            <?php
            $campaing = App\Util\Helpers::getCurrentCampaign();
            echo substr($campaing, 0, 4) . '-' . substr($campaing, 4, 2)
            ?>            
        </span>
    </div>
    @endif
</div>
<br />
<!-- END PAGE TITLE-->
<!-- END PAGE HEADER-->