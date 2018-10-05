/**
 *  Infinite scroller paginator.
 *  This directive allows to paginate the data using the scroller, when the scroller
 *  is getting closer to the end, the directive calls the controller that is using it
 *  with the next page.
 *
        <div infinite-scroll="usersCtr.nextPage($page)">
            <div ng-repeat="users in users">
            </div>
        </div>

        class UsersController{
            constructor(Paginator){
                Paginator.start(); //<--start the infinite scroller
            }
            nextPage(page){
                //load the next page using the "page" param
                
                Paginator.getPage(); //<--You can also use the Paginator service to get the page
            }
        }
 *
 */
let InfiniteScroll = ($timeout,Paginator) => {
    return {
        restrict: 'A',
        link    : function(scope, element, attrs) {
            var container = element[0],
                percentage= 0.3;        //at 30% before getting to the end, the callback is called.

            element.bind('scroll', onScrollListener);

            scope.$on('$destroy', function() {
                element.unbind('scroll', onScrollListener);
            });

            function onScrollListener(event){
                let el      = event.target,
                    before  = el.clientHeight * percentage,
                    height  = el.scrollHeight;

                if((height - el.scrollTop - before) <= el.clientHeight) {
                    if (!Paginator.isLoaded(height)) {
                        Paginator.nextPage();
                        Paginator.addLoaded(height, Paginator.getPage());


                        scope.$page = Paginator.getPage();
                        scope.$apply(attrs.infiniteScroll);
                    }
                }
            }
        }
    };
}

function debounce(fn, delay) {
    var timer = null;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fn.apply(context, args);
        }, delay);
    };
}

InfiniteScroll.$inject = ['$timeout','Paginator'];

export default InfiniteScroll;