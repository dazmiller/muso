
let EN = ($translateProvider) => {
  $translateProvider.translations('en', {
    AUTH_CREATE_ACCOUNT: 'Create account',
    AUTH_DONT_HAVE: 'Don\'t have an account?',
    AUTH_EMAIL: 'Email',
    AUTH_EMAIL_REQUIRED: 'The email is required, please fill out this field',
    AUTH_FORGOT_DESCRIPTION: 'Enter your email and we will send you a link to update your password',
    AUTH_FORGOT_PASSWORD: 'Forgot your password?',
    AUTH_LOGIN: 'Login',
    AUTH_LOGIN_DESCRIPTION: 'In order to access all features, you need to login.',
    AUTH_LOGIN_FACEBOOK: 'Login with Facebook',
    AUTH_NAME: 'Full name',
    AUTH_NAME_REQUIRED: 'Your full name is required, please fill out this field',
    AUTH_PASSWORD: 'Password',
    AUTH_PASSWORD_REQUIRED: 'The password is required, please fill out this field.',
    AUTH_RECOVER: 'Recover Password',
    AUTH_REGISTER: 'Register',
    AUTH_RETURN_HOME: 'Back to home',
    AUTH_RETURN_LOGIN: 'Back to login',
    AUTH_SIGNUP_DESCRIPTION: 'Join the community and discover new music everyday!',
    AUTH_UPDATE_PASSWD_DESCRIPTION: 'Welcome {{name}}! Update your password here',
    AUTH_UPDATE_PASSWD: 'Update password',
  });
}

EN.$inject = ['$translateProvider'];

export default EN;
