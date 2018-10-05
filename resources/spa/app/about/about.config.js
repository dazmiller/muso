// Import the template for this module
import './about.tpl.html';

let Config = ($stateProvider) => {

    $stateProvider
    // If the page is going to be available to any visitor,
    // we need to use the `public` namespace, then the name
    // of our new module, in this case `public.about`
        .state('public.about', {
            // Then we need to define the PATH for this module
            url: "/about",
            views: {
                'main@': {
                    // The template to use for this module, this
                    // will point to the new template file we have created
                    templateUrl: 'about/about.tpl.html',
                    // The controller's name to use for this module
                    controller: 'AboutController',
                    // We can create an alias for this controller,
                    // using this name is how we are going to
                    // access the controller from the template
                    controllerAs: 'aboutCtl'
                }
            }
        });
}

// Inject the dependencies for the configurations
Config.$inject = ['$stateProvider'];

// And finally export it!
export default Config;