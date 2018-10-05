/**
 *  File Model.
 *  This directive allows to listen on file input changes, when a user selects a new file 
 *  the content will be assigned to the model.
 *
        <input type="file" file-model="controller.user.avatar">

        class UsersController{
            constructor($scope){
                $scope.$watch('controller.user.avatar',function(newValue,oldValue){

                    //do whatever you need with the selected new file

                })
            }
        }
 *
 */
let FileModel = ($parse) => {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', onChangeFile);

            scope.$on('$destroy', function() {
                element.unbind('change', onChangeFile);
            });

            function onChangeFile(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            }
        }
    };
}

FileModel.$inject = ['$parse'];

export default FileModel;