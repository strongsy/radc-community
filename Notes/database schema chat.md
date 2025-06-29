User:
Please design a database schema for the attached tables. Generate laravel migration files, factories with seeders, model relationships and any other useful information to create a successful app.
DB Design
Users
When registering a user, we need to store the following fields:

```markdown
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

```


Assistant:
# Database Schema Design for RADC Veterans Community

I'll design a complete database schema for the RADC Veterans Community application with Laravel migrations, factories, seeders, and model relationships.

## Database Schema Overview

![Database Schema Diagram]
- Main entities: Users, Events, Posts, Articles, Stories, Galleries
- Supporting entities: Comments, Images, Likes, Event Attendees, Event Guests, Categories
- Polymorphic relationships for shared functionality (comments, images, likes)

## Migration Files

Let's create the necessary migration files for our database schema:

<llm-snippet-file>database/migrations/2014_10_12_000000_create_users_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('community', ['Serving', 'Reserve', 'Veteran', 'Civilian', 'Other']);
            $table->enum('membership', ['Life', 'Annual', 'Unsure']);
            $table->enum('grade', ['Officer', 'WO/SNCO', 'JNCO/Pte'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_subscribed')->default(true);
            $table->string('unsubscribe_token')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000001_create_categories_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // 'event', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000002_create_events_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('location');
            $table->enum('type', ['public', 'private'])->default('public');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('user_id')->constrained()->comment('Organizer');
            $table->boolean('guests_allowed')->default(false);
            $table->integer('max_guests_per_user')->default(0);
            $table->integer('max_attendees')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000003_create_event_attendees_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_attending')->default(true);
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000004_create_event_guests_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->comment('User who is bringing the guest');
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_guests');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000005_create_posts_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('user_id')->constrained()->comment('Author');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000006_create_articles_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('user_id')->constrained()->comment('Author');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000007_create_stories_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('user_id')->constrained()->comment('Author');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000008_create_galleries_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->comment('Author');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000009_create_comments_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->comment('Commenter');
            $table->morphs('commentable'); // This creates commentable_id and commentable_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000010_create_images_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('size')->comment('Size in bytes');
            $table->text('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->foreignId('user_id')->constrained()->comment('Uploader');
            $table->morphs('imageable'); // This creates imageable_id and imageable_type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
```

<llm-snippet-file>database/migrations/2023_06_01_000011_create_likes_table.php</llm-snippet-file>
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->morphs('likeable'); // This creates likeable_id and likeable_type
            $table->timestamps();

            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
```

## Model Definitions

Now, let's define the models with their relationships:

<llm-snippet-file>app/Models/User.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'community',
        'membership',
        'grade',
        'is_active',
        'is_blocked',
        'is_subscribed',
        'unsubscribe_token',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'unsubscribe_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_blocked' => 'boolean',
        'is_subscribed' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the events organized by the user.
     */
    public function organizedEvents()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the events the user is attending.
     */
    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_attendees')
            ->withPivot('is_attending')
            ->withTimestamps();
    }

    /**
     * Get the guests the user has invited to events.
     */
    public function eventGuests()
    {
        return $this->hasMany(EventGuest::class);
    }

    /**
     * Get the posts created by the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the articles created by the user.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get the stories created by the user.
     */
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    /**
     * Get the galleries created by the user.
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Get the comments created by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the images uploaded by the user.
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get the likes given by the user.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
```

<llm-snippet-file>app/Models/Category.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    /**
     * Get the events in this category.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
```

<llm-snippet-file>app/Models/Event.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'location',
        'type',
        'status',
        'category_id',
        'user_id',
        'guests_allowed',
        'max_guests_per_user',
        'max_attendees',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'guests_allowed' => 'boolean',
        'max_guests_per_user' => 'integer',
        'max_attendees' => 'integer',
    ];

    /**
     * Get the organizer of the event.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category of the event.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the attendees of the event.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withPivot('is_attending')
            ->withTimestamps();
    }

    /**
     * Get the guests invited to the event.
     */
    public function guests()
    {
        return $this->hasMany(EventGuest::class);
    }

    /**
     * Get the comments for the event.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the images for the event.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the likes for the event.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```

<llm-snippet-file>app/Models/EventAttendee.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventAttendee extends Pivot
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'is_attending',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_attending' => 'boolean',
    ];
}
```

<llm-snippet-file>app/Models/EventGuest.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'guest_name',
        'guest_email',
    ];

    /**
     * Get the event that the guest is invited to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who invited the guest.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

<llm-snippet-file>app/Models/Post.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'user_id',
    ];

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the images for the post.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the likes for the post.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```

<llm-snippet-file>app/Models/Article.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the comments for the article.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the images for the article.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the likes for the article.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```

<llm-snippet-file>app/Models/Story.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'user_id',
    ];

    /**
     * Get the author of the story.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the comments for the story.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the images for the story.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the likes for the story.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```

<llm-snippet-file>app/Models/Gallery.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    /**
     * Get the author of the gallery.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the images for the gallery.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the likes for the gallery.
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
```

<llm-snippet-file>app/Models/Comment.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Get the user who created the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent commentable model (post, event, etc.).
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
```

<llm-snippet-file>app/Models/Image.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'alt_text',
        'caption',
        'user_id',
        'imageable_id',
        'imageable_type',
    ];

    /**
     * Get the user who uploaded the image.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent imageable model (post, event, etc.).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
```

<llm-snippet-file>app/Models/Like.php</llm-snippet-file>
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    /**
     * Get the user who created the like.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent likeable model (post, event, etc.).
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
```

## Factory Classes

Let's create factory classes for generating test data:

<llm-snippet-file>database/factories/UserFactory.php</llm-snippet-file>
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'community' => fake()->randomElement(['Serving', 'Reserve', 'Veteran', 'Civilian', 'Other']),
            'membership' => fake()->randomElement(['Life', 'Annual', 'Unsure']),
            'grade' => fake()->randomElement(['Officer', 'WO/SNCO', 'JNCO/Pte']),
            'is_active' => true,
            'is_blocked' => false,
            'is_subscribed' => true,
            'unsubscribe_token' => Str::random(32),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
```

<llm-snippet-file>database/factories/CategoryFactory.php</llm

