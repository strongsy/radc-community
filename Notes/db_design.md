# DB Design

## Users
When registering a user, we need to store the following fields:
- Full name
- Password
- Email
- Community (Serving, Reserve, Veteran, Civilian, Other)
- Membership (Life, Annual, Unsure)
- Grade (Officer, WO/SNCO, JNCO/Pte)
- is_active (false if user is not active)
- is_blocked (true if user is blocked)
- is_subscribed (true if user is subscribed to newsletter)
- unsubscribe_token (token to unsubscribe)

## Events
Users can attend events and if alowed, can bring guests.
- event title
- event description
- event date
- event time
- event location
- event type (public, private)
- event status (pending, approved, rejected) other tables will have status.
- event guests (list of guests)
- event attendees (list of attendees)
- event organizer (user)
- event comments (list of comments) other tables will have comments.
- event images (list of images) other tables will have images.
- event likes (list of likes) other tables will have likes.
- event category (sports, music, arts, etc)

## Posts
Users can post content.
- post title
- post content
- post date
- post status (pending, approved, rejected) other tables will have status.
- post author (user)
- post comments (list of comments) other tables will have comments.
- post images (list of images) other tables will have images.
- post likes (list of likes) other tables will have likes.

## Articles
Admins can create articles.
- article title
- article content
- article date
- article author (user)
- article comments (list of comments) other tables will have comments.
- article images (list of images) other tables will have images.
- article likes (list of likes) other tables will have likes.

## Stories
Users can post stories.
- story title
- story content
- story date
- story status (pending, approved, rejected) other tables will have status.
- story author (user)
- story comments (list of comments) other tables will have comments.
- story images (list of images) other tables will have images.
- story likes (list of likes) other tables will have likes.

## Galleries
Admins can create galleries.
- gallery title
- gallery description
- gallery date
- gallery author (user)
- gallery images (list of images) other tables will have images.
- gallery likes (list of likes) other tables will have likes.

Any other tables join tables for, events, posts, articles, galleries, guests, attendees, comments, images, likes.
