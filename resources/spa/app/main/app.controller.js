import AbstractController from '../common/controllers/abstract.controller';
import PlaylistController from '../playlists/playlists.form.controller';
import Connection from '../common/utils/connection';

class AppController extends AbstractController{
    
    /**
     * Set's the timer ID to constantly check if there are new unread
     * messages for the given user.
     */
    unreadTimer = null;

    constructor($timeout, $interval, $rootScope, $state, $translate, $sce, $mdDialog, $auth, $mdSidenav, User, Playlist, MailBox, Song){
        super(AppController.$inject,arguments);

        this.TITLE = Configurations.APP_TITLE;
        this.downloable = Configurations.APP_DOWNLOAD_SONG_FILE;

        //1.-   Allow to view the public areas, restrict all others if there's
        //      not a logged user.
        $rootScope.$on('$stateChangeStart', (event, toState, toParams, fromState, fromParams) => {
            let namespace = toState.name.split('.');

            if(namespace[0] !== 'public' && !User.isSessionValid()){
                event.preventDefault();
                $state.go('public.auth');
            }

        });

        //2.-   Try to get the current logged user (if logged)
        if(User.isSessionValid()){
            User.requestCurrentUser()
                .then((data) => {
                    this.user = data;
                    $auth.user = data;
                    this.startUnreadTimer();

                    return Promise.resolve();
                })
                .then(() => Playlist.all())
                .then(({ playlists }) => {
                    this.user.playlists = playlists;
                });
        }


        //3.-   Initialize the player
        this.player = {
            hidden  : true,
            repeat: false,
        };

        //4.-   On login success we need to set the user data to this controller
        //      in order to display the user's information in the main views.
        //      The 'login-success' event is fired by the auth controller when user successfully logged in
        $rootScope.$on('login-success', (event,user) => {
            this.user = user;
            $auth.user = user;
            User.setCurrent(user);
            this.startUnreadTimer();
        });

        //5.-   Other modules can play songs, we will listen to those events
        $rootScope.$on('play-song', (event, song, playlist) => {
            this.playSong(song, playlist);
        });

        // 6.-  Listener when a message is read, we need to make sure
        //      to decrement the counter if needed.
        $rootScope.$on('mailbox-read', (event, thread) => {
            if (this.threads && this.threads.items) {
                const unread = this.threads.items.find(t => t.id === thread.id);

                if (unread && this.threads.total > 0) {
                    this.threads.items = this.threads.items.filter(t => t.id !== thread.id);
                    this.threads.total = this.threads.total - 1;
                }
            }
        });
        
        $rootScope.$on('loading', (event, loading) => {
            this.mode = loading? 'query' : '';
            this.isLoading = loading;
        });
    }

    logout(){
        this.user = null;
        this.threads = {};
        this.User.logout();
        this.$state.go('public.discover');

        // Stop the timer to check new messages
        if (angular.isDefined(this.unreadTimer)) {
            this.$interval.cancel(this.unreadTimer);
            this.unreadTimer = undefined;
        }
    }

    startUnreadTimer() {
        // Stop current timer if defined already
        if (angular.isDefined(this.unreadTimer)) {
            this.$interval.cancel(this.unreadTimer);
        }

        this.loadUnreadEmails()
        this.unreadTimer = this.$interval(() => {
            this.loadUnreadEmails()
        }, 60000);
    }

    loadUnreadEmails() {
        this.MailBox.unread({ perPage: 5, mainmenu: true })
            .then(({ threads, meta }) => {
                this.threads = {
                    total: meta.total,
                    items: threads,
                };
            });
    }

    showProfile(id){
        this.$state.go('members.profiles',{id:id});
    }
    
    showInbox(){
        this.$state.go('members.mailbox.inbox');
    }

    showSettings(){
        this.$state.go('members.profiles.settings',{id:this.user.id});
    }

    onPlayerReady($API){
        this.PlayerAPI = $API;
    }

    getCurrentIndex() {
        return this.playlist && this.playlist.findIndex(song => song.id === this.currentSong.id);
    }

    playPrevSong() {
        let index = this.getCurrentIndex();

        if (index > -1) {
            index -= 1;

            if (index > -1) {
                this.playSong(this.playlist[index], this.playlist);
            }
        }
    }

    playNextSong() {
        let index = this.getCurrentIndex();
        if (index > -1) {
            index += 1;

            if (index < this.playlist.length) {
                this.playSong(this.playlist[index], this.playlist);
            } if (this.player.repeat && index === this.playlist.length) {
                this.playSong(this.playlist[0], this.playlist);
            }
        }
    }

    playSong(song, playlist){
        let url = song.sound;
        this.PlayerAPI.stop();
        this.currentSong = song;
        this.playlist = playlist || [];

        if (typeof url !== 'string') {
            url = url.url;
        }

        this.player = {
            ...this.player,
            hidden: false,
            sources: [
                { src: this.$sce.trustAsResourceUrl(`${url}?token=${this.$auth.getToken()}`), type: "audio/mpeg"}
            ]
        };
        this.$timeout(this.PlayerAPI.play.bind(this.PlayerAPI), 100);
        this.GoogleTagManager.event({
            name: 'play',
            category: 'Song',
            label: song.title,
            value: song.id,
        });
    }

    toggleRepeat() {
        this.player.repeat = !this.player.repeat;
    }

    search(query) {
        this.$state.go('public.search', { query });
    }

    addPlaylist(event, playlist, song){
        if (!this.user) {
          this.$state.go('public.auth');
          return;
        }

        this.$mdDialog.show({
                controller    : PlaylistController,
                controllerAs  : 'playlistFormCtrl',
                templateUrl   : 'playlists/playlists.form.tpl.html',
                targetEvent   : event,
                locals: {
                    playlist,
                },
                bindToController: true,
            })
            .then((playlist) => {
                this.user.playlists = this.user.playlists || [];
                const index = this.user.playlists.findIndex(list => list.id === playlist.id);

                if (index >= 0) {
                    this.user.playlists[index] = playlist;
                } else {
                    this.user.playlists.push(playlist);
                }

                if (song) {
                    this.Playlist.addSong({
                        playlistId: playlist.id,
                        songId: song.id,
                    });
                } else {
                    this.$state.go('members.playlists', { id: playlist.id });
                }
            });
    }

    removePlaylist(playlist, event) {
        var confirm = this.$mdDialog.confirm()
            .title(this.$translate.instant('ARE_YOU_SURE'))
            .textContent(this.$translate.instant('PLAYLIST_DELETE_WARNING'))
            .ariaLabel('Delete playlist')
            .targetEvent(event)
            .ok(this.$translate.instant('YES'))
            .cancel(this.$translate.instant('NO'));

        this.$mdDialog.show(confirm)
            .then(() => {
                this.Playlist.remove(playlist)
                    .then(() => {
                        this.user.playlists = this.user.playlists.filter(item => item.id !== playlist.id);
                        this.$state.go('public.discover');
                    });
            });
    }

    toggleMenu() {
        this.$mdSidenav('menu').toggle();
    }

    download(song) {
        this.Song.download(song);
        this.GoogleTagManager.event({
            name: 'play',
            category: 'Song',
            label: song.title,
            value: song.id,
        });
    }
}

AppController.$inject = ['$timeout', '$interval', '$rootScope', '$state', '$translate', '$sce', '$mdDialog', '$auth', '$mdSidenav', 'User', 'Playlist', 'MailBox', 'Song', 'GoogleTagManager'];

export default AppController;
