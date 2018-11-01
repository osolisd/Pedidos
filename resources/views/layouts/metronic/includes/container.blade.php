<div class="container">
    <div class="page-container">

        <!-- BEGIN SIDEBAR -->
        @include('layouts.metronic.includes.sidebar')
        <!-- END SIDEBAR -->

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">

            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">

                @include('layouts.metronic.includes._breadcrumb', [
                'title' => $title,
                'controller' => $controller,
                'view' => $view
                ])

                @include('layouts.metronic.includes._messages')

                @include('layouts.metronic.includes._errors')

                @yield('content')          

            </div>
            <!-- END CONTENT BODY -->

        </div>
        <!-- END CONTENT -->

    </div>
</div>