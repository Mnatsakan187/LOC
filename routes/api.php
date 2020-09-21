<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//



Route::group(['middleware' => 'guest:api'], function () {
    Route::group(['prefix' => '/v1'], function () {
         Route::post     ('user'                                 , 'UserController@register');
         Route::get      ('user/validate/{token}'                , 'UserController@verify');
         Route::post     ('user/login'                           , 'UserController@authenticate') ;
         Route::post     ('user/validate/email'                  , 'UserController@validateUserEmail');
         Route::post     ('user/send/reset/email'                , 'UserController@sendResetPasswordEmail');
         Route::get      ('user/password'                        , 'UserController@sendResetPasswordEmail');
         Route::post     ('user/password-reset'                  , 'UserController@resetPassword');
         Route::post     ('stripe/api'                           , 'StripePaymentController@stripePost');
         Route::get      ('auth/facebook'                        , 'UserController@redirectToFacebook');
         Route::get      ('auth/facebook/callback'               , 'UserController@handleFacebookCallback');

         Route::get      ('auth/twitter'                         , 'UserController@redirectToTwitter');
         Route::get      ('auth/twitter/callback'                , 'UserController@handleTwitterCallback');
         Route::get      ('auth/linkedin'                        , 'UserController@redirectToLinkedin');
         Route::get      ('auth/linkedin/callback'               , 'UserController@handleLinkedinCallback');

         Route::get      ('auth/instagram'                        , 'UserController@redirectToInstagram');
         Route::get      ('auth/instagram/callback'               , 'UserController@handleInstagramCallback');

        Route::get       ('plans'    ,'PlanController@index');
    });
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::group(['prefix' => '/v1'], function () {

         //Users
         Route::get     ('users'                                 , 'UserController@index');
         Route::get     ('user'                                  , 'UserController@show');
         Route::post    ('user/update'                           , 'UserController@update');
         Route::put     ('user/change-password'                  , 'UserController@chageUserPassword');
         Route::post    ('user/logout'                           , 'UserController@userLogout');
         Route::delete  ('user'                                  , 'UserController@destroy');
         Route::put     ('user/update/first-time-user'           , 'UserController@updateFirstTimeUser');

         //Profiles
         Route::get     ('user/profiles'                                 ,'ProfileController@index');
         Route::get     ('user/profiles/{profileId}'                     ,'ProfileController@show');
         Route::post    ('user/profiles'                                 ,'ProfileController@store');
         Route::post    ('user/profiles/update/{profileId}'              ,'ProfileController@update');
         Route::delete  ('user/profiles/{profileId}'                     ,'ProfileController@destroy');
         Route::get     ('user/profiles/{profileId}/view'                ,'ProfileController@profileView');
         Route::get     ('user/profiles/columns/{profileId}/updated'     ,'ProfileController@updateProfileColumn');
         Route::put     ('/user/profiles/hide/update'                    ,'ProfileController@updateHide');
         Route::get     ('/profiles/subscription'                        ,'ProfileController@getUserProfileSubscription');

         //Projects
         Route::get     ('user/profiles/{profileId}/projects'                         ,'ProjectController@index');
//         Route::get     ('user/profiles/{profileId}/projects/{projectId}'             ,'ProjectController@show');
        Route::get     ('user/projects/{projectId}'             ,'ProjectController@show');
         Route::post    ('user/profiles/{profileId}/projects'                         ,'ProjectController@store');
         Route::post    ('user/profiles/{profileId}/projects/{projectId}/update'      ,'ProjectController@update');
         Route::delete  ('user/profiles/{profileId}/projects/{projectId}'             ,'ProjectController@destroy');
         Route::get     ('user/projects'                                              ,'ProjectController@getProjects');
         Route::get     ('user/projects/{projectId}/{filter}'                         ,'ProjectController@getOneProject');
         Route::post    ('user/projects/{projectId}/share'                            ,'ProjectController@updateShareCount');
         Route::get     ('user/projects/column/{projectId}/update'                    ,'ProjectController@updateProjectColumn');
         Route::get     ('user/{userId}/projects'                                     ,'ProjectController@getUserProjects');
         Route::get     ('user/co-creators'                                           ,'ProjectController@getCoCreators');
         Route::get     ('user/project/{projectId}/co-creators'                       ,'ProjectController@getProjectCoCreators');
         Route::get     ('user/auth/user/project'                                     ,'ProjectController@getAuthUserProjects');
         Route::get     ('/projects/subscription'                                     ,'ProjectController@getUserProjectSubscription');
         Route::post    ('user/project/pin-to-top'                                    ,'ProjectController@pinToTop');


         //Groups
         Route::get     ('user/groups'                                 , 'GroupController@index');
         Route::get     ('user/groups/{groupId}'                       , 'GroupController@show');
         Route::get     ('user/groups/{groupId}/members'               , 'GroupController@groupMembers');
         Route::post    ('user/groups'                                 , 'GroupController@store');
         Route::put     ('user/groups/{groupId}'                       , 'GroupController@update');
         Route::delete  ('user/groups/{groupId}'                       , 'GroupController@destroy');
         Route::delete  ('user/groups/{groupId}/members/{memberId}'    , 'GroupController@destroyGroupMember');
         Route::post    ('user/groups/{groupId}/members'               , 'GroupController@storeMember');
         Route::get     ('/groups/subscription'                        ,'GroupController@getUserGroupSubscription');


         //Tags
         Route::get     ('user/tags'                             ,'TagController@index');
         Route::get     ('user/tags/{tagId}'                     ,'TagController@show');
         Route::post    ('user/tags'                             ,'TagController@store');
         Route::put     ('user/tags/{tagId}'                     ,'TagController@update');
         Route::delete  ('user/tags/{tagId}'                     ,'TagController@destroy');


        //Collections
         Route::get     ('user/profiles/{profileId}/collections'                        ,'CollectionController@index');
         Route::get     ('user/profiles/{profileId}/collections/{collectionId}'         ,'CollectionController@show');
         Route::post    ('user/profiles/{profileId}/collections'                        ,'CollectionController@store');
         Route::post    ('user/profiles/{profileId}/collections/{collectionId}/update'  ,'CollectionController@update');
         Route::delete  ('user/profiles/{profileId}/collections/{collectionId}'         ,'CollectionController@destroy');


        //Collections of Profiles
        Route::get     ('user/collections/{collectionId}/profiles'                    ,'CollectionProfileController@index');
        Route::get     ('user/collections/{collectionId}/profiles/{profileId}'        ,'CollectionProfileController@show');
        Route::post    ('user/collections/{collectionId}/profiles/{profileId}'        ,'CollectionProfileController@store');
        Route::delete  ('user/collections/{collectionId}/profiles/{profileId}'        ,'CollectionProfileController@destroy');

        //Collections of Project
        Route::get     ('user/collections/{collectionId}/projects'                    ,'CollectionProjectController@index');
        Route::get     ('user/collections/{collectionId}/projects/{projectId}'        ,'CollectionProjectController@show');
        Route::post    ('user/collections/{collectionId}/projects/{projectId}'        ,'CollectionProjectController@store');
        Route::delete  ('user/collections/{collectionId}/projects/{projectId}'        ,'CollectionProjectController@destroy');

        //Collections of Event
        Route::get     ('user/collections/{collectionId}/events'                  ,'CollectionEventController@index');
        Route::get     ('user/collections/{collectionId}/events/{eventId}'        ,'CollectionEventController@show');
        Route::post    ('user/collections/{collectionId}/events/{eventId}'        ,'CollectionEventController@store');
        Route::delete  ('user/collections/{collectionId}/events/{eventId}'        ,'CollectionEventController@destroy');


        //Collections of Creator
        Route::get     ('user/collections/{collectionId}/creators'                  ,'CollectionCreatorController@index');
        Route::get     ('user/collections/{collectionId}/creators/{creatorId}'      ,'CollectionCreatorController@show');
        Route::post    ('user/collections/{collectionId}/creators/{creatorId}'      ,'CollectionCreatorController@store');
        Route::delete  ('user/collections/{collectionId}/creators/{creatorId}'      ,'CollectionCreatorController@destroy');

        //TagProject
        Route::get     ('user/projects/{projectId}/tags'             ,'TagProjectController@index');
        Route::get     ('user/projects/{projectId}/tags/{tagId}'     ,'TagProjectController@show');
        Route::post    ('user/projects/{projectId}/tags'             ,'TagProjectController@store');
        Route::put     ('user/projects/{projectId}/tags/{tagId}'     ,'TagProjectController@update');
        Route::delete  ('user/projects/{projectId}/tags/{tagId}'     ,'TagProjectController@destroy');

        //TagPost
        Route::get     ('user/posts/{postId}/tags'             ,'TagPostController@index');
        Route::get     ('user/posts/{postId}/tags/{tagId}'     ,'TagPostController@show');
        Route::post    ('user/posts/{postId}/tags'             ,'TagPostController@store');
        Route::put     ('user/posts/{postId}/tags/{tagId}'     ,'TagPostController@update');
        Route::delete  ('user/posts/{postId}/tags/{tagId}'     ,'TagPostController@destroy');

        //TagEvent
        Route::get     ('user/events/{eventId}/tags'             ,'TagEventController@index');
        Route::get     ('user/events/{eventId}/tags/{tagId}'     ,'TagEventController@show');
        Route::post    ('user/events/{eventId}/tags'             ,'TagEventController@store');
        Route::put     ('user/events/{eventId}/tags/{tagId}'     ,'TagEventController@update');
        Route::delete  ('user/events/{eventId}/tags/{tagId}'     ,'TagEventController@destroy');

        //Events
        Route::get     ('user/profiles/{profileId}/events'                         ,'EventController@index');
        Route::get     ('user/profiles/{profileId}/events/{eventId}'               ,'EventController@show');
        Route::post    ('user/profiles/{profileId}/events'                         ,'EventController@store');
        Route::post    ('user/profiles/{profileId}/events/{eventId}/update'        ,'EventController@update');
        Route::delete  ('user/profiles/{profileId}/events/{eventId}'               ,'EventController@destroy');
        Route::get     ('user/events/calendar'                                     ,'EventController@getEvents');
        Route::get     ('user/events'                                              ,'EventController@events');
        Route::get     ('user/events/{eventId}'                                    ,'EventController@getOneEvent');
        Route::post    ('user/events/map'                                          ,'EventController@getEventsOnMap');
        Route::post    ('user/events/list'                                         ,'EventController@getEventsList');

        //Posts
        Route::get     ('user/posts'                   ,            'PostController@index');
//        Route::get     ('user/profiles/{profileId}/posts/{postId}'          ,'PostController@show');
        Route::get     ('user/posts/{postId}'                               ,'PostController@show');
//        Route::post    ('user/profiles/{profileId}/posts'                   ,'PostController@store');
        Route::post    ('user/posts'                                        , 'PostController@store');
//        Route::post    ('user/profiles/{profileId}/posts/{postId}/update'   ,'PostController@update');
        Route::post    ('user/posts/{postId}/update'   ,                'PostController@update');
//        Route::delete  ('user/profiles/{profileId}/posts/{postId}'          ,'PostController@destroy');
        Route::delete  ('user/posts/{postId}'          ,                    'PostController@destroy');
        Route::post    ('user/posts/{postId}/share'                         ,'PostController@updateShareCount');
        Route::post    ('user/posts/pin-to-top'                             ,'PostController@pinToTop');


        //LikeProject
        Route::get     ('user/projects/{projectId}/likes'            ,'LikeProjectController@index');
        Route::get     ('user/projects/{projectId}/likes/{likeId}'   ,'LikeProjectController@show');
        Route::post    ('user/projects/{projectId}/likes'            ,'LikeProjectController@store');
        Route::delete  ('user/projects/{projectId}/likes/{likeId}'   ,'LikeProjectController@destroy');

        //LikeEvent
        Route::get     ('user/events/{eventId}/likes'                ,'LikeEventController@index');
        Route::get     ('user/events/{eventId}/likes/{likeId}'       ,'LikeEventController@show');
        Route::post    ('user/events/{eventId}/likes'                ,'LikeEventController@store');
        Route::delete  ('user/events/{eventId}/likes/{likeId}'       ,'LikeEventController@destroy');

        //LikePoll
        Route::get     ('user/polls/{pollId}/likes'                ,'LikePollController@index');
        Route::get     ('user/polls/{pollId}/likes/{likeId}'       ,'LikePollController@show');
        Route::post    ('user/polls/{pollId}/likes'                ,'LikePollController@store');
        Route::delete  ('user/polls/{pollId}/likes/{likeId}'       ,'LikePollController@destroy');

        //LikePost
        Route::get     ('user/posts/{postId}/likes'                  ,'LikePostController@index');
        Route::get     ('user/posts/{postId}/likes/{likeId}'         ,'LikePostController@show');
        Route::post    ('user/posts/{postId}/likes'                  ,'LikePostController@store');
        Route::delete  ('user/posts/{postId}/likes/{likeId}'         ,'LikePostController@destroy');

        //MediaProject
        Route::get     ('user/projects/{projectId}/media'                     ,'MediaProjectController@index');
        Route::get     ('user/projects/{projectId}/media/{mediaId}'           ,'MediaProjectController@show');
        Route::post    ('user/projects/{projectId}/media'                     ,'MediaProjectController@store');
        Route::post    ('user/projects/{projectId}/media/{mediaId}/update'    ,'MediaProjectController@update');
        Route::delete  ('user/projects/{projectId}/media/{mediaId}'           ,'MediaProjectController@destroy');

        //MediaPost
        Route::get     ('user/posts/{postId}/media'              ,'MediaPostController@index');
        Route::get     ('user/posts/{postId}/media/{mediaId}'    ,'MediaPostController@show');
        Route::post    ('user/posts/{postId}/media'              ,'MediaPostController@store');
        Route::post     ('user/posts/{postId}/media/update'    ,'MediaPostController@update');
        Route::delete  ('user/posts/{postId}/media/{mediaId}'    ,'MediaPostController@destroy');

        //MediaEvent
        Route::get     ('user/events/{eventId}/media'              ,'MediaEventController@index');
        Route::get     ('user/events/{eventId}/media/{mediaId}'    ,'MediaEventController@show');
        Route::post    ('user/events/{eventId}/media'              ,'MediaEventController@store');
        Route::put     ('user/events/{eventId}/media/{mediaId}'    ,'MediaEventController@update');
        Route::delete  ('user/events/{eventId}/media/{mediaId}'    ,'MediaEventController@destroy');

        //MediaProfile
        Route::get     ('user/profiles/{profileId}/media'                      ,'MediaProfileController@index');
        Route::get     ('user/profiles/{profileId}/media/{mediaId}'            ,'MediaProfileController@show');
        Route::post    ('user/profiles/{profileId}/media'                      ,'MediaProfileController@store');
        Route::post    ('user/profiles/{profileId}/media/{mediaId}/update'     ,'MediaProfileController@update');
        Route::delete  ('user/profiles/{profileId}/media/{mediaId}'            ,'MediaProfileController@destroy');

        //MediaUser
        Route::get     ('user/users/{userId}/media'                      ,'MediaUserController@index');
        Route::get     ('user/users/{userId}/media/{mediaId}'            ,'MediaUserController@show');
        Route::post    ('user/users/{userId}/media'                      ,'MediaUserController@store');
        Route::post    ('user/users/{userId}/media/{mediaId}/update'     ,'MediaUserController@update');
        Route::delete  ('user/users/{userId}/media/{mediaId}'            ,'MediaUserController@destroy');

        //Hud
        Route::get     ('user/hud'                      ,'HudController@index');
        Route::get     ('user/hud/search'               ,'HudController@searchHud');
        Route::get     ('user/loc'                      ,'HudController@getLoc');
        Route::get     ('user/discover'                 ,'HudController@discover');


        //CommentProject
        Route::get     ('user/projects/{projectId}/comments'                 ,'CommentProjectController@index');
        Route::get     ('user/projects/{projectId}/comments/{commentId}'     ,'CommentProjectController@show');
        Route::post    ('user/projects/{projectId}/comments'                 ,'CommentProjectController@store');
        Route::put     ('user/projects/{projectId}/comments/{commentId}'     ,'CommentProjectController@update');
        Route::delete  ('user/projects/{projectId}/comments/{commentId}'     ,'CommentProjectController@destroy');

        //CommentPost
        Route::get     ('user/posts/{postId}/comments'                 ,'CommentPostController@index');
        Route::get     ('user/posts/{postId}/comments/{commentId}'     ,'CommentPostController@show');
        Route::post    ('user/posts/{postId}/comments'                 ,'CommentPostController@store');
        Route::put     ('user/posts/{postId}/comments/{commentId}'     ,'CommentPostController@update');
        Route::delete  ('user/posts/{postId}/comments/{commentId}'     ,'CommentPostController@destroy');

        //CommentEvent
        Route::get     ('user/events/{eventId}/comments'                 ,'CommentEventController@index');
        Route::get     ('user/events/{eventId}/comments/{commentId}'     ,'CommentEventController@show');
        Route::post    ('user/events/{eventId}/comments'                 , 'CommentEventController@store');
        Route::put     ('user/events/{eventId}/comments/{commentId}'     ,'CommentEventController@update');
        Route::delete  ('user/events/{eventId}/comments/{commentId}'     ,'CommentEventController@destroy');

        //CommentPoll
        Route::get     ('user/polls/{pollId}/comments'                 ,'CommentPollController@index');
        Route::get     ('user/polls/{pollId}/comments/{commentId}'     ,'CommentPollController@show');
        Route::post    ('user/polls/{pollId}/comments'                 ,'CommentPollController@store');
        Route::put     ('user/polls/{pollId}/comments/{commentId}'     ,'CommentPollController@update');
        Route::delete  ('user/polls/{pollId}/comments/{commentId}'     ,'CommentPollController@destroy');


        //Replies
        Route::get     ('user/comments/{commentId}/replies'                  ,'ReplyController@index');
        Route::get     ('user/comments/{commentId}/replies/{replyId}'        ,'ReplyController@show');
        Route::post    ('user/comments/{commentId}/replies'                  ,'ReplyController@store');
        Route::put     ('user/comments/{commentId}/replies'                  ,'ReplyController@update');
        Route::delete  ('user/comments/{commentId}/replies/{replyId}'        ,'ReplyController@destroy');


        //MailChimp Api
        Route::post    ('mail-chimp/store'                      ,'MailChimpController@store');

        //Stripe Api
        Route::post     ('stripe'                           ,'StripePaymentController@stripePost');

        //Polls
        Route::get     ('user/polls'                                ,'PollController@index');
        Route::get     ('user/polls/{pollId}'                       ,'PollController@show');
        Route::post    ('user/polls'                                ,'PollController@store');
        Route::put     ('user/polls/{pollId}'                       ,'PollController@update');
        Route::delete  ('user/polls/{pollId}'                       ,'PollController@destroy');
        Route::get     ('user/polls'                                ,'PollController@getPolls');
        Route::get     ('user/polls/diagram/{pollId}'               ,'PollController@getPollDiagram');
        Route::post    ('user/polls/pin-to-top'                     ,'PollController@pinToTop');


        //User Poll Answer
        Route::post     ('user/polls/{pollId}/answers'                       ,'UserPollAnswerController@store');


        //Favourite
        Route::get     ('user/get/favourite/projects'                      ,'FavouriteController@getFavouriteProjects');
        Route::get     ('user/followers/profiles'                      ,'FavouriteController@getFollowedCreators');

        //Categories
        Route::get     ('categories'                      ,'CategoryController@index');

        //Follow
        Route::get     ('user/follows'                  ,'FollowController@index');
        Route::get     ('user/follows/{followId}'       ,'FollowController@show');
        Route::post    ('user/follows/{followId}'       ,'FollowController@store');
        Route::delete  ('user/follows/{followId}'       ,'FollowController@destroy');
        Route::get     ('user/follows'                  ,'FollowController@getFollowers');


        //Mailshakes
        Route::get('/mailshakes',   'MailshakeController@index');
        Route::post('/mailshakes/send', 'MailshakeController@sendMailshake');

        //NotificationProject
        Route::get     ('user/notifications'                    ,'NotificationProjectController@index');
        Route::get     ('user/notifications/{notificationId}'   ,'NotificationProjectController@show');
        Route::post    ('user/notifications'                    ,'NotificationProjectController@store');
        Route::delete  ('user/notifications/{notificationId}'   ,'NotificationProjectController@destroy');
        Route::post    ('notifications/read'                    ,'NotificationProjectController@markedRead');
        Route::get     ('notifications/read/{id}'                ,'NotificationProjectController@markedReadById');


        //TeamProject
        Route::get     ('user/projects/team/{projectId}/teams'       ,'TeamProjectController@index');
        Route::get     ('user/projects/{projectId}/teams/{userId}'   ,'TeamProjectController@show');
        Route::post    ('user/projects/{projectId}/teams'            ,'TeamProjectController@store');
        Route::delete  ('user/projects/{projectId}/teams/{userId}'   ,'TeamProjectController@destroy');

        //TeamProfile
        Route::get     ('user/profiles/{profileId}/teams'            ,'TeamProfileController@index');
        Route::get     ('user/profiles/{profileId}/teams/{userId}'   ,'TeamProfileController@show');
        Route::post    ('user/profiles/{profileId}/teams'            ,'TeamProfileController@store');
        Route::delete  ('user/profiles/{profileId}/teams/{userId}'   ,'TeamProfileController@destroy');

        //Messages
        Route::get     ('user/messages/{toUserId}'             ,'MessageController@index');
        Route::get     ('user/messages/{messageId}/show'       ,'MessageController@show');
        Route::post    ('user/messages'                        ,'MessageController@store');
        Route::put     ('user/messages/{messageId}'            ,'MessageController@update');
        Route::delete  ('user/messages/{messageId}'            ,'MessageController@destroy');

        //Networks
        Route::get     ('user/networks'                        ,'NetworkController@index');
        Route::get     ('user/networks/{networkId}'            ,'NetworkController@show');
        Route::post    ('user/networks'                        ,'NetworkController@store');
        Route::put     ('user/networks/{networkId}'            ,'NetworkController@update');
        Route::delete  ('user/networks/{networkId}'            ,'NetworkController@destroy');

        //Connection
        Route::get     ('user/connection'                           ,'ConnectController@index');
        Route::get     ('user/connection/{profileId}'               ,'ConnectController@show');
        Route::post    ('user/connection/{profileId}'               ,'ConnectController@store');
        Route::delete  ('user/connection/{profileId}'               ,'ConnectController@destroy');
        Route::post    ('user/connection/{notificationId}/accept'   ,'ConnectController@accept');
        Route::post    ('user/connection'                           ,'ConnectController@connectionProfile');

        Route::get    ('user/plans'    ,'PlanController@index');

        Route::get    ('user/plans'    ,'PlanController@index');


    });
});




