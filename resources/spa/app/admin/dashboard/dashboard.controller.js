import AbstractController from '../../common/controllers/abstract.controller';
import Chartist from 'chartist';

const Months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

class AdminDashboardController extends AbstractController{
  constructor(){
    super(AdminDashboardController.$inject, arguments);
  }

  index(params){
    this.loadOverviewData();
    this.loadStatiscsData();
    this.loadPopularSongs();
    this.loadPopularArtists();
    this.loadPopularAlbums();
  }

  loadPopularSongs() {
    this.Statistics.show('popular/songs?limit=5')
      .then(({ songs }) => {
        this.topSongs = songs;
      });
  }
  
  loadPopularArtists() {
    this.Statistics.show('popular/artists?limit=5')
      .then(({ artists }) => {
        this.topArtists = artists.map(artist => ({
          ...artist,
          plays: parseInt(artist.plays, 10),
        }));
      });
  }
  
  loadPopularAlbums() {
    this.Statistics.show('popular/albums?limit=5')
      .then(({ albums }) => {
        this.topAlbums = albums.map(album => ({
          ...album,
          plays: parseInt(album.plays, 10),
        }));;
      });
  }

  // Load data for top widgets
  loadOverviewData() {
    this.Statistics.show('overview')
      .then(({ data }) => {
        this.overview = data;
      });
  }

  // Load data for line chart
  loadStatiscsData() {
    this.Statistics.all()
      .then(({ data }) => {
        // 1.- Preparing data for chart
        const series = {
          play: [],
          like: [],
          unlike: [],
          download: [],
        };

        data.forEach((item) => {
          if (series[item.action]) {
            const x = Date.UTC(item.created_year, item.created_month - 1, 10);
            series[item.action].push({
              x,
              y: item.total,
            });
          }
        });

        // 2.- Setting up options
        this.options = {
          axisX: {
            type: Chartist.FixedScaleAxis,
            divisor: Math.max(series.play.length, series.like.length, series.download.length) - 1,
            labelInterpolationFnc: function (value) {
              const date = new Date(value);
              return `${Months[date.getUTCMonth()]} ${date.getUTCFullYear()}`;
            }
          },
        };

        // 3.- Setting up events
        this.events = {
          draw: (item) => {
            if (item.type === 'point') {
              item.element._node.onmouseover = () => {
                this.tooltip = {
                  position: {
                    top: `${item.y - 30}px`,
                    left: `${item.x + 5}px`,
                  },
                  label: `${item.series.title}s: ${item.value.y.toLocaleString()}`,
                };
                this.$scope.$apply();
              };

              item.element._node.onmouseout = () => {
                this.tooltip = undefined;
                this.$scope.$apply();
              };
            }
          },
        };

        // 4.- Adding the series to the chart
        this.data = {
          series: Object.keys(series).map(title => ({
              title,
              data: series[title],
          })),
        };

        // 5.- Adding totals for each serie
        this.totals = [
          {
            title: 'Plays',
            key: 'plays',
            total: series.play.reduce((total, num) => {
              return total + num.y;
            }, 0),
          },
          {
            title: 'Likes',
            key: 'likes',
            total: series.like.reduce((total, num) => {
              return total + num.y;
            }, 0),
          },
          {
            title: 'Unlikes',
            key: 'unlikes',
            total: series.unlike.reduce((total, num) => {
              return total + num.y;
            }, 0),
          },
          {
            title: 'Downloads',
            key: 'downloads',
            total: series.download.reduce((total, num) => {
              return total + num.y;
            }, 0),
          }
        ];
      });
  }
}

AdminDashboardController.$inject = ['$scope', '$state', '$translate', 'Statistics'];

export default AdminDashboardController;