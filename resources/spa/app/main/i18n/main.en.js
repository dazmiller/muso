
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        YES         : 'Yes',
        NO          : 'No',
        ACCEPT      : 'Accept',
        CANCEL      : 'Cancel',
        EXPLORE     : 'Explore',
        SEARCH      : 'Search',
        SEARCH_SONGS: 'Search songs, albums or artists...',
        AUTHORS     : 'Authors',
        DASHBOARD   : 'Dashboard',
        MY_MUSIC    : 'My Music',
        PLAYLISTS   : 'Playlists',
        DISCOVER    : 'Discover',
        ADMIN       : 'Admin',
        USERS       : 'Users',
        CONFIGURATIONS: 'Configurations',
        DASHBOARD   : 'Dashboard',
        ALBUMS      : 'Albums',
        COMMENTS    : 'Comments',
        TRENDING    : 'Trending',
        GENRES      : 'Genres',
        ARTISTS     : 'Artists',
        VIDEOS      : 'Videos',
        BLOG        : 'Blog',
        YOUR_MUSIC  : 'Your Music',
        FAVORITES   : 'Favorites',
        HISTORY     : 'History',
        ARE_YOU_SURE: 'Are you sure?',
        ABOUT : "About",


        MENU_VIEW_MORE: 'View all messages...',
        MENU_YOUR_PROFILE: 'Your Profile',
        MENU_INBOX: 'Inbox',
        MENU_CHANGE_SETTINGS: 'Change your Settings',
        MENU_LOGOUT: 'Logout',

        ERROR_SOMETHING_WRONG   : 'Something went wrong, please try again.',
        ERROR_ACCESS_DENIED     : "Seems like you are trying to access a restricted area, please don't do it.",
        ERROR_NOT_LOGGED        : "To access this area, you need to login."
    });
}

EN.$inject = ['$translateProvider'];

export default EN;