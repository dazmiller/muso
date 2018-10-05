import 'angular-material/angular-material.css';

import 'core-js/es6/promise';
import 'whatwg-fetch';

import Angular from 'angular';
import Material from 'angular-material';
import UIRouter from 'angular-ui-router';
import SocialShare from 'angular-socialshare';
import I18N from 'angular-translate';

import MusicAdminUser from './admin/users';
import MusicAdminAlbums from './admin/albums';
import MusicAdminDashboard from './admin/dashboard';
import MusicAdminGenres from './admin/genres';
import MusicAdminComments from './admin/comments';
import MusicAdminPosts from './admin/posts';
import MusicAdminConfig from './admin/configurations';

import MusicConfig from './app.config';
import MusicRun from './app.run';
import MusicAuth from './auth';
import MusicMain from './main';

import AuthorsDashboard from './authors/dashboard';
import AuthorsMyMusic from './authors/mymusic';

import MembersProfiles from './profiles';
import MembersFavorites from './favorites';
import MembersHistory from './history';
import MembersPlaylists from './playlists';
import MembersMailbox from './mailbox';

import MusicIcons from './icons';
import MusicDiscover from './discover';
import MusicAlbums from './albums';
import MusicSongs from './songs';
import MusicExplore from './explore';
import MusicArtists from './artists';
import MusicVideos from './videos';
import MusicBlog from './blog';
import MusicSearch from './search';
import MusicAbout from './about';
Angular.module('Music', [
    Material,
    UIRouter,
    SocialShare,
    I18N,

    MusicAuth,
    MusicMain,

    MusicAdminUser,
    MusicAdminAlbums,
    MusicAdminDashboard,
    MusicAdminGenres,
    MusicAdminComments,
    MusicAdminPosts,
    MusicAdminConfig,

    AuthorsDashboard,
    AuthorsMyMusic,
    
    MembersProfiles,
    MembersFavorites,
    MembersHistory,
    MembersPlaylists,
    MembersMailbox,

    MusicIcons,
    MusicAlbums,
    MusicDiscover,
    MusicSongs,
    MusicExplore,
    MusicArtists,
    MusicVideos,
    MusicBlog,
    MusicSearch,
    MusicAbout,
])
.config(MusicConfig)
.run(MusicRun);