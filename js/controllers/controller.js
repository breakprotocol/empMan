var globalfunction = {};
myApp.controller('DashboardCtrl', function ($scope, TemplateService, NavigationService, $timeout, $state) {
        //Used to name the .html file
        $scope.template = TemplateService.changecontent("dashboard");
        $scope.menutitle = NavigationService.makeactive("Dashboard");
        TemplateService.title = $scope.menutitle;
        $scope.navigation = NavigationService.getnav();
    })

    .controller('headerctrl', function ($scope, TemplateService, $uibModal, $state) {
        $scope.template = TemplateService;
        $scope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
            $(window).scrollTop(0);
        });

        var profile = $.jStorage.get("profile");
        console.log("profile", _.isEmpty(profile));
        if (_.isEmpty(profile)) {
            $state.go('login')
        }

    })

    .controller('AccessController', function ($scope, TemplateService, NavigationService, $timeout, $state) {
        // if ($.jStorage.get("accessToken")) {

        // } else {
        //     $state.go("login");
        // }
    })

    .controller('LoginCtrl', function ($scope, TemplateService, NavigationService, $uibModal, $state, toastr) {
        $.jStorage.flush();
        $scope.template = TemplateService;
        $scope.formData = {};
        $scope.goRegister = function () {
            $state.go("register");
        }
        $scope.login = function (formData) {
            console.log(formData);
            $scope.LoginFailurebool = false;
            NavigationService.login('/login/login', formData, function (data) {
                console.log(data.success);
                if (data.success == "true" || data.success == true) {
                    $.jStorage.set("profile", {
                        'credentials': data.access
                    });
                    $state.go("contacts-entry-user");
                } else {
                    console.log("data.success");
                    $scope.LoginFailurebool = true;
                    $scope.LoginFailureMessage = "Incorrect Creds,Go send the valid user!!";
                    toastr.error("Incorrect Login Credentials");
                }

            });
        }
    })

    .controller('LoginToCpanelCtrl', function ($scope, TemplateService, NavigationService, $uibModal, $state, toastr) {
        $.jStorage.flush();
        $scope.template = TemplateService;
        $scope.formData = {};
        $scope.goRegister = function () {
            $state.go("register");
        }
        $scope.login = function (formData) {
            console.log(formData);
            $scope.LoginFailurebool = false;
            NavigationService.login('/login/login', formData, function (data) {
                console.log(data.success);
                if (data.success == "true" && data.access == "999") {
                    $.jStorage.set("profile", {
                        'credentials': data.access
                    });
                    $state.go("dashboard");
                    toastr.success("Login Successfull");
                } else {
                    $scope.LoginFailurebool = true;
                    $scope.LoginFailureMessage = "Incorrect Creds,Go send the valid user!!";
                    toastr.error("Incorrect Login Credentials");
                }

            });
        }
    })

    .controller('languageCtrl', function ($scope, TemplateService, $translate, $rootScope) {

        $scope.changeLanguage = function () {
            console.log("Language CLicked");

            if (!$.jStorage.get("language")) {
                $translate.use("hi");
                $.jStorage.set("language", "hi");
            } else {
                if ($.jStorage.get("language") == "en") {
                    $translate.use("hi");
                    $.jStorage.set("language", "hi");
                } else {
                    $translate.use("en");
                    $.jStorage.set("language", "en");
                }
            }
            //  $rootScope.$apply();
        };
    });