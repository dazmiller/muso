
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        //tabs
        PROFILE_ACTIVITY    : 'Activity',
        PROFILE_MY_ALBUMS   : 'My Albums',
        PROFILE_MY_PLAYLISTS: 'My Playlists',
        PROFILE_MY_SONGS    : 'Latest Songs',
        PROFILE_SETTINGS    : 'Settings',

        //main page
        PROFILE_ABOUT_ME    : 'About me',
        PROFILE_FOLLOW_ME   : 'Follow me',
        PROFILE_CHANGE_IMAGE: 'Change Avatar',
        PROFILE_SEND_MESSAGE: 'Send message',
        PROFILE_DEFAULT_LOCATION    : 'Citizen of the world',
        PROFILE_DEFAULT_OCCUPATION  : 'Music Lover',
        PROFILE_DEFAULT_ABOUT       : 'I love music! I think is a great way to express ourselves.',

        //general information form
        PROFILE_GENERAL_INFORMATION : 'General Information',
        PROFILE_NAME        : 'Your full name',
        PROFILE_OCCUPATION  : 'Occupation',
        PROFILE_DOB         : 'Date of Birth',
        PROFILE_EMAIL       : 'Your Email',
        PROFILE_RESIDENCY   : 'Where do you live?',
        PROFILE_WEBSITE     : 'Do you have a personal website?',
        PROFILE_ABOUT       : 'Tell us a little bit about yourself.',

        //change password form
        PROFILE_CHANGE_PASSWORD     : 'Change Password',
        PROFILE_USER        : 'Your Username',
        PROFILE_PASWD       : 'Password',
        PROFILE_PASWD_REPEAT: 'Confirm Password',
        PROFILE_CHANGE_FREQUENTLY : "It's recommended to change your password frequently.",

        PROFILE_MY_FOLLOWERS: 'Followers',
        PROFILE_MY_FOLLOWING: 'Following',
        PROFILE_UNFOLLOW: 'Unfollow',
        PROFILE_FOLLOWERS_NONE: 'doesn\'t have any followers, you should be the first one to follow!',
        PROFILE_FOLLOWINGS_NONE: 'is not following anybody!',

        //Activities
        ACTIVITIES_LOAD_MORE    : 'Load More',
        ACTIVITY_AVATAR         : 'Uploaded a new profile picture.',
        ACTIVITY_ALBUM_BLOG     : 'Published the album',
        ACTIVITY_COMMENT_BLOG   : 'Left a comment on the post',
        ACTIVITY_COMMENT_SONG   : 'Left a comment on the song',
        ACTIVITY_DOWNLOAD_SONG  : 'Downloaded the song',
        ACTIVITY_PUBLISHED_BLOG : 'Published the post',
        ACTIVITY_GENERAL_INFO   : 'Updated the general information.',
        ACTIVITY_LIKE_SONG      : 'Liked the song',
        ACTIVITY_PLAY_SONG      : 'Played the song',
        ACTIVITY_UNLIKE_SONG    : 'Unliked the song',
        ACTIVITY_WELCOME        : 'Has joined the community, welcome!',
        ACTIVITY_FOLLOWED: 'has been followed by',
        ACTIVITY_FOLLOW: 'Is now following',
        ACTIVITY_UNFOLLOW: 'Unfollowed',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;