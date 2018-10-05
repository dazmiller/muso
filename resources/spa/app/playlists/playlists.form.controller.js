
const PlaylistsFormController = ($scope, $mdDialog, Playlist) => {

  $scope.cancel = ()=>{
    $mdDialog.cancel();
  };

  $scope.save = (model)=>{
    if (model && model.id) {
      Playlist.update(model)
        .then(({ playlist }) => {
          $mdDialog.hide(model);
        });
    } else {
      Playlist.store(model)
        .then(({ playlist })=>{
          $mdDialog.hide(playlist);
        });
    }
  };

}

PlaylistsFormController.$inject = ['$scope', '$mdDialog', 'Playlist'];

export default PlaylistsFormController;