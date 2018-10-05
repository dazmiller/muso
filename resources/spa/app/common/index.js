import Angular from 'angular';
import Satellizer from 'satellizer';

//services
import Connection from './services/connection.service';
import Configuration from './services/configuration.service';
import User from './services/user.service';
import Album from './services/album.service';
import Song from './services/song.service';
import File from './services/file.service';
import Genre from './services/genre.service';
import MailBox from './services/mailbox.service';
import Paginator from './services/paginator.service';
import Comment from './services/comment.service';
import Playlist from './services/playlist.service';
import Post from './services/post.service';
import Statistics from './services/statistics.service';
import Tag from './services/tag.service';
import GoogleTagManager from './services/googletagmanager.service';

//controllers
import AbstractController from './controllers/abstract.controller'

//directives
import Accordion from './directives/Accordion';
import Chartist from './directives/Chartist';
import ErrorMessages from './directives/ErrorMessages';
import FileModel from './directives/FileModel';
import MiniPlayer from './directives/MiniPlayer';
import MusicWaves from './directives/MusicWaves';
import InfiniteScroll from './directives/InfiniteScroll';
import AddToPlaylist from './directives/AddToPlaylist';
import MediumEditor from './directives/MediumEditor';
import CommentList from './directives/comments/CommentList';
import CommentForm from './directives/comments/CommentForm';
import CommentEN from './directives/comments/i18n/comments.en';
import UsersWidget from './directives/UsersWidget';
import OnImageError from './directives/OnImageError';
import ShareButton from './directives/ShareButton';
import StatPanel from './directives/StatPanel';

//filters
import Ellipsis from './filters/ellipsis.js';
import RemoveHtml from './filters/removeHtml.js';
import TimeAgo from './filters/timeAgo.js';
import Duration from './filters/duration.js';
import MainTitle from './filters/mainTitle.js';
import SetFullSongUrl from './filters/setFullSongUrl.js';

//styles
import './styles/admin.scss';
import './styles/components.scss';

let module = Angular.module('Music.common', [
        Satellizer
    ])
    .controller('AbstractController',AbstractController)
    .factory('Connection', Connection)
    .factory('Configuration', Configuration)
    .factory('User', User)
    .factory('Album', Album)
    .factory('Song', Song)
    .factory('Statistics', Statistics)
    .factory('Tag', Tag)
    .service('File', File)
    .service('Genre', Genre)
    .service('MailBox', MailBox)
    .service('Paginator', Paginator)
    .service('Comment', Comment)
    .service('Playlist', Playlist)
    .service('Post', Post)
    .service('GoogleTagManager', GoogleTagManager)
    .directive('addToPlaylist',AddToPlaylist)
    .directive('chartist', Chartist)
    .directive('accordion', Accordion)
    .directive('shareButton', ShareButton)
    .directive('statPanel', StatPanel)
    .directive('errorMessages', ErrorMessages)
    .directive('fileModel',FileModel)
    .directive('musicWaves', MusicWaves)
    .directive('miniPlayer', MiniPlayer)
    .directive('infiniteScroll',InfiniteScroll)
    .directive('mediumEditor',MediumEditor)
    .directive('commentList',CommentList)
    .directive('commentForm',CommentForm)
    .directive('usersWidget', UsersWidget)
    .directive('onImageError', OnImageError)
    .filter('ellipsis',Ellipsis)
    .filter('removeHtml',RemoveHtml)
    .filter('timeAgo', TimeAgo)
    .filter('duration', Duration)
    .filter('mainTitle', MainTitle)
    .filter('setFullSongUrl', SetFullSongUrl)
    .config(CommentEN)
    .name;

export default module;