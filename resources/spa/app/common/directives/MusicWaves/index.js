import './styles.scss';

var MusicWaves = () => {
  return {
    restrict: 'E',
    template: [
      '<div class="music-waves">',
      '<span></span>',
      '<span></span>',
      '<span></span>',
      // '<span></span>',
      // '<span></span>',
      '</div>',
    ].join(''),
  };
};

MusicWaves.$inject = [];

export default MusicWaves;