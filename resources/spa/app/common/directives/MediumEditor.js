'use strict';
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

import Medium from 'medium-editor';
import 'medium-editor/dist/css/medium-editor.css';
import 'medium-editor/dist/css/themes/default.css';

let MediumEditor = () => {

    var toInnerText = (value) => {
        var tempEl = document.createElement('div'),
            text;

        tempEl.innerHTML = value;
        text = tempEl.textContent || '';
      
        return text.trim();
    }

    return {
        restrict: 'E',
        require: 'ngModel',
        scope: { bindOptions: '=' },
        link: function(scope, element, attrs, ngModel) {
            angular.element(element).addClass('angular-medium-editor');

            // Global MediumEditor
            ngModel.editor = new Medium(element, scope.bindOptions);

            ngModel.$render = function() {
                element.html(ngModel.$viewValue || "");
                ngModel.editor.getExtensionByName('placeholder').updatePlaceholder(element[0]);
            };

            ngModel.$isEmpty = function(value) {
                if (/[<>]/.test(value)) {
                    return toInnerText(value).length === 0;
                } else if (value) {
                    return value.length === 0;
                } else {
                    return true;
                }
            };

            ngModel.editor.subscribe('editableInput', function (event, editable) {
                ngModel.$setViewValue(editable.innerHTML.trim());
            });

            scope.$watch('bindOptions', function(bindOptions) {
                ngModel.editor.init(element, bindOptions);
            });
        }
    };
}

MediumEditor.$inject = [];

export default MediumEditor;