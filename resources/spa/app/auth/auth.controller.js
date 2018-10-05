import AbstractController from '../common/controllers/abstract.controller';

class AuthController extends AbstractController{

    constructor($rootScope, $state, $auth, User, $window){
        super(AuthController.$inject, arguments);

        this.loginForm = {};
        this.facebookEnabled = $window.Configurations.APP_FACEBOOK_ENABLED;

        //in case the login url is hit but there's a valid session
        if($auth.isAuthenticated()){
            $state.go('public.discover');
        }
        
        if ($state.current.name === 'public.auth.recovery') {
            this.User.recoverPassword($state.params.token)
                .then((response) => {
                    this.$auth.setToken({ data: { token: response.token } });
                    this.$rootScope.$emit('login-success', response.user);
                    this.user = response.user;
                })
                .catch((response) => {
                    this.errors = response.data.errors;
                    this.disabled = true;
                });
        }
    }
    
    login() {
        this.errors = null;
        this.$auth.login(this.loginForm)
            .then((response) => { 
                this.$rootScope.$emit('login-success',response.data.user);
                this.$state.go('public.discover');
            })
            .catch((response) => { 
                this.errors = response.data.errors;
            });
    }

    loginFacebook(){
        this.errors = null;
        this.$auth.authenticate('facebook')
            .then((response) => {
                this.$rootScope.$emit('login-success',response.data.user);
                this.$state.go('public.discover');
            })
            .catch((error) => {
                console.log('error', error);
                // $scope.error = error.errors[0];
            });
    }
    
    signup() {
        this.errors = null;
        this.$auth.signup(this.loginForm)
            .then((response) => {
                this.$auth.setToken(response);
                this.$rootScope.$emit('login-success', response.data.user);
                this.$state.go('public.discover');
            })
            .catch((response) => {
                this.errors = response.data.errors;
            });
    }

    forgot() {
        this.errors = null;
        this.User.forgotPassword(this.loginForm)
            .then(
                (response) => {
                    this.loginForm = {};
                    this.errors = [response.message];
                },
                (response) => {
                    this.errors = response.errors;
                }
            );
    }

    savePasswd() {
        this.User.updatePasswd(this.user)
            .then((response) => {
                this.$state.go('public.discover');
            })
            .catch((response) => {
                this.errors = response.errors;
            });
    }
}

AuthController.$inject = ['$rootScope', '$state', '$auth', 'User', '$window'];

export default AuthController;