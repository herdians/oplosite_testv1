<!DOCTYPE html>
<html lang="en">
<style>
.modal-header {
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #0480be;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
     color: #fff;
}
</style>

@include('commons.header')

<body>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                @include('admin.sidebar')
                <div class="right_col" role="main">
                    <div class="">
                        @yield('content')
                    </div>
                </div>

            </div>
        </div>
        @include('commons.footer')
    </body>
</body>
