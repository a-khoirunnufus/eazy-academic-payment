<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <title>EAZY</title>
    <link rel="stylesheet" media="screen" href="{{ url('css/login-new.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" type="text/css" href="{{ url('css/style.css') }}?version={{ config('version.css_style') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/vuexy/css/plugins/extensions/ext-component-toastr.css') }}">

    <style>
        .sign-up .auth-sidebar {
            background: #bae9ff;
            color: #0078c0;
        }

        .auth-sidebar header .logo {
            opacity: 1;
        }

        input[type='checkbox']:checked+label:before {
            background: #FF4949 url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iMTIiIHZpZXdCb3g9IjAgMCAxMiAxMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMTAuNjQwMiAyLjIzNjQ4QzEwLjIxNjIgMS44NzU5OCA5LjU4NTcgMS45MzQ0OCA5LjIzMTcgMi4zNjc0OEw0LjgwNzIgNy43ODQ5OEwyLjU1NTIgNi4yNTI5OEMyLjA5NjIgNS45Mzk5OCAxLjQ3NDcgNi4wNjY5OCAxLjE2ODcgNi41MzU5OEMwLjg2MjcwMSA3LjAwNDk4IDAuOTg2NzAxIDcuNjM4NDggMS40NDYyIDcuOTUwOThMNS4xOTMyIDEwLjVMMTAuNzY4NyAzLjY3MzQ4QzExLjEyMjIgMy4yNDA0OCAxMS4wNjQ3IDIuNTk3NDggMTAuNjQwMiAyLjIzNjQ4WiIgZmlsbD0id2hpdGUiLz48L3N2Zz4=");
            background-repeat: no-repeat;
            background-position: center center;
            -webkit-transition: none;
            transition: none;
            border: unset
        }

        .text-blue {
            color: #138EFF
        }

        .bg-blue {
            background: #138EFF
        }

        .text-input:focus, .text-input:hover {
            border: 1px solid #FF4949 !important;
            color:#FF4949 !important
        }

        .text-input:focus + label, .text-input:hover + label {
            border: 1px solid #FF4949 !important;
            color:#FF4949 !important
        }

        .text-red-orange {
            color:#FF4949 !important
        }

        .btn-red-orange {
            background: #FF4949 !important;
            color: white
        }

        .btn-outline-red-orange {
            border: 1.5px solid #FF4949 !important;
            color: #FF4949
        }
    </style>
</head>

<body class="logged-out not-pro not-player not-self not-team not-on-team  sign-up">
    <div class="w-100 h-100">
        <div class="row w-100 h-100">
            <div class="col-lg-6 p-3 py-5 d-flex flex-column">
                <div class="w-100 h-100 px-4 mx-auto d-flex flex-column" style="max-width: 500px">
                    <div class="d-flex align-items-center mb-5">
                        <img style="max-width:100px" class="w-100" src="{{ asset('images/logo-eazy-small.png') }}" />
                    </div>
                    <div class="my-auto">
                        <h1 style="font-weight: 700" class="mb-2 text-red-orange">Welcome Back!</h1>
                        <p class="mb-5" style="">
                            <strong>Welcome to Eazy</strong>, Please Input Your Login Credentials Below To Start Using This App.
                        </p>
                        <form id="login-form" onsubmit="return _loginRequest()">
                            <div class="form-field">
                                <div class="form-group">
                                    <label class="text-muted d-flex align-items-center justify-content-between">
                                        <span>Email</span><span class="text-red-orange">*</span>
                                    </label>
                                    <input class="text-input" type="text" name="email"/>
                                </div>
                            </div>
                            <div class="form-field">
                                <fieldset class="form-group">
                                    <label class="text-muted d-flex align-items-center justify-content-between">
                                        <span>Password</span><span class="text-red-orange">*</span>
                                    </label>
                                    <input  class="text-input" placeholder="6+ characters" type="password" name="password"/>
                                </fieldset>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-field check-wrap opt-in">
                                    <fieldset>
                                        <input type="checkbox" class="bg-blue" name="user[agree_to_terms]" />
                                        <label for="user_agree_to_terms">
                                            Keep me signed in
                                        </label>
                                    </fieldset>
                                </div>
                                <a>
                                    <label class="text-red-orange" style="font-size: 14px">Forgot Password?</label>
                                </a>
                            </div>
                            <div class="mt-2">
                                <button id="submit-button" class="btn btn-red-orange w-100" style="font-size: 14px" type="submit">
                                    <span style="font-weight: 700">Login</span>
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <h6 class="text-muted" style="font-size: 14px !important">
                                    Not have an account?
                                    <strong class="" style="color:#163485 !important;text-decoration: underline">
                                        Create account
                                    </strong>
                                </h6>
                            </div>
                        </form>
                    </div>
                    <div>
                        <hr style="border-color: #FF4949;border-width:1pt" />
                    </div>
                    <div class="mt-5 d-flex flex-wrap justify-content-between">
                        <h6 class="text-muted" style="font-size: 13px !important;color:#163485 !important;">
                            @ @php date('Y'); @endphp EAZY. Allrights reserved.
                        </h6>
                        <h6 class="text-muted" style="font-size: 13px !important;color:#163485 !important;">
                            Terms of Service ● Privacy Policy
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 p-3 hidden-xs" style="background:#FAEFF1;box-shadow: 3px 20px 30px rgba(0, 0, 0, .16);">
                <div class="d-flex flex-column w-100 h-100">
                    <div class="d-flex flex-column mx-auto h-100" style="max-width: 800px">
                        <div class="d-flex flex-column mt-auto align-items-center">
                            <div class="my-auto ">
                                <img class="w-100" src="{{url('images/login-item.png')}}" />
                            </div>
                            <div class="text-center mt-5" style="max-width: 500px">
                                <h1 class="h4" style="font-weight: 700;color:#163485">
                                    Eazy Can Help To Fulfill The Academic And Administrative Needs Of The University.
                                </h1>
                                <p class="mb-3 text-secondary" style="">
                                    Action is the fundamental key to all success.
                                </p>
                            </div>
                        </div>
                        <div class="mx-auto my-5 d-flex" style="gap:10px">
                            <div style="width:40px;height:40px;background:#FBBFC0;box-shadow: 3px 4px 8px rgba(0, 0, 0, 0.16);" class="text-white rounded-circle d-flex">
                                <i class="bx bx-chevron-left m-auto h3"></i>
                            </div>
                            <div style="width:40px;height:40px;background:#FBBFC0;box-shadow: 3px 4px 8px rgba(0, 0, 0, 0.16);" class="text-white rounded-circle d-flex">
                                <i class="bx bx-chevron-right m-auto h3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Script Sections  -->
    <script>
        const _baseURL = "{{ url('/') }}"
        const _csrfToken = "{{ csrf_token() }}"
    </script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="{{ asset('/themes/vuexy/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/form-data-json-convert/dist/form-data-json.min.js"></script>
    
    <script src="{{ url('js/app.js') }}?version={{ config('version.js_config') }}"></script>
    <script>
        const _loginRequest = () => {
            let formRequest = FormDataJson.toJson("#login-form")

            _ajaxConfig.setButtonAsLoadingIndicator("#submit-button")

            $.post(_baseURL + '/login', formRequest, (response) => {
                window.location = response.redirectURL
            }).fail((error) => {
                _responseHandler.formFailResponse(error)
            })

            return false
        }
    </script>

    
</body>

</html>
