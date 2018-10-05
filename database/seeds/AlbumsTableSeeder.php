<?php

use Illuminate\Database\Seeder;
use App\Http\Helpers\MP3Helper;
use App\User;
use App\Album;
use App\Song;
use App\File as FileEntry;
use Ramsey\Uuid\Uuid;

class AlbumsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // DB::table('albums')->truncate();
       // DB::table('songs')->truncate();

        $tags = [
            'soft', 'relax', 'children', 'happiness', 'energetic',
            'active', 'lively', 'dynamic', 'spirited', 'animated', 'vibrant', 'exuberant', 'enthusiastic',
            'joyful', 'cheerful', 'happy', 'jolly', 'merry', 'sunny', 'lighthearted',
            'sorrow', 'dejection', 'depression', 'misery', 'despondency', 'despair', 'desolation', 'sadness',
            'emotional', 'spiritual', 'inner', 'psychological', 'psychic',
        ];
        $authors = User::take(20)->get();
        $i = 1;
        $indexForTitles = -1;
        foreach ($authors as $author) {
            $author->author = true;
            $author->save();

            $album = $author->albums()->save(factory(Album::class)->make([
                'genre_id'  => rand(1,20) //only 20 genres seeded in GenresTableSeeder.php
            ]));

			
			/*
            $pathArt = 'users/'.$author->id.'/albums/'.$album->id.'/art';
            mkdir(storage_path().'/app/public/'.$pathArt, 0777, true);
            copy(storage_path().'/seed/album-art/'.$i.'.jpeg', storage_path().'/app/public/'.$pathArt.'/'.$i.'.jpeg');

            $art = new FileEntry();
            $art->fill([
                'id'            => Uuid::uuid1(),
                'fileable_type' => 'App\\Album',
                'fileable_id'   => $album->id,
                'name'          => $i.'.jpeg',
                'original_name' => $i.'.jpeg',
                'content_type'  => 'image/jpeg',
                'size'          => filesize(storage_path().'/seed/album-art/'.$i.'.jpeg'),
                'path'          => $pathArt
            ]);
            $art->save();
*/
            // Creating songs
            $pathSound = 'users/'.$author->id.'/albums/'.$album->id.'/tracks';
            mkdir(storage_path().'/app/public/'.$pathSound, 0777, true);
            
            $totalSongs = rand(2, 8);
            for ($k = 0; $k < $totalSongs; $k++) {
                $fileName = rand(1, 12);
                $randomTags = array_rand(array_flip($tags), rand(2, 5));
                $song = $album->songs()->save(factory(Song::class)->make());
                $song->setTags(implode(",", $randomTags));

                // Get the mp3 duration
                try {
                    $mp3 = new MP3Helper(storage_path().'/seed/sounds/'.$fileName.'.mp3');
                    $duration = $mp3->getDurationEstimate();
                    $duration = MP3Helper::formatTime($duration);
                } catch (Exception $e) {
                    $duration = '00:00:00';
                }
                $indexForTitles = $indexForTitles + 1;
                if ($indexForTitles > count($this->titles)) {
                    $indexForTitles = 0;
                }
                
                $song->title = $this->titles[$indexForTitles];
                $song->duration = $duration;
                $song->save();

                copy(storage_path().'/seed/sounds/'.$fileName.'.mp3',storage_path().'/app/public/'.$pathSound.'/'.$fileName.'_'.$k.'.mp3');
                $sound = new FileEntry();
                $sound->fill([
                    'id'            => Uuid::uuid1(),
                    'fileable_type' => 'App\\Song',
                    'fileable_id'   => $song->id,
                    'name'          => $fileName.'_'.$k.'.mp3',
                    'original_name' => $fileName.'_'.$k.'.mp3',
                    'content_type'  => 'audio/mp3',
                    'size'          => filesize(storage_path().'/app/public/'.$pathSound.'/'.$fileName.'_'.$k.'.mp3'),
                    'path'          => $pathSound
                ]);
                $sound->public = false;
                $sound->duration = $duration;
                $sound->save();
            }

            $i++;
            if($i>20){
                $i=1;
            }
        }
    }

    private $titles = [
        "As she passes",
        "Beethoven - Moonlight Sonata (Glenn playing on a Steinway D)",
        "Ludwig Van Beethoven-- Fur Elise",
        "Bedtime Baby Lullaby Classical Music Mozart Bach Beethoven Pachelbel Sleep Music 1 Hour",
        "SAD PIANO - Violin -  Cant Forgive 용서 못해 (Ballad)",
        "Brahms Lullaby And White Noise Ocean Waves",
        "song of storms piano again",
        "The Heart of Reiki",
        "Naruto ~ Sadness and Sorrow",
        "Twilight - Bella's Lullaby - Original Piano",
        "Mozart - Requiem in D minor Complete Full",
        "Chopin Nocturne in F Minor-Op. 55, No 1 (Variation) Piano, Violin, Cello - Chad Lawson",
        "Brahms' Lullaby - Orchestral Musicbox Lullaby For Babies - (Free Download)",
        "Titanic Theme Song \"My Heart Will Go On\" - Flute Instrumental - Karin Leitner",
        "Where is my mind (The Pixies cover)",
        "Afreen Afreen, Rahat Fateh Ali Khan & Momina Mustehsan, Episode 2, Coke Studio 9",
        "Steve Gibbs - Patterns (Cyrus Reynolds Remix)",
        "Calvin Harris Ft. Frank Ocean & Migos - Slide",
        "Clair de Lune",
        "Moonlight Sonata",
        "Cold  - Jorge Méndez (Sad Piano & Violin Instrumental)",
        "We Move Lightly",
        "Chopin - Nocturne Op. 9 No. 2",
        "Jeff The Killer - Sweet Dreams (Marilyn Manson & Creepypasta Tribute)",
        "Clair de lune - Debussy",
        "Remember Me - Coco",
        "Tambour - The Nude And The Quiet",
        "Raga Bhairavi [Mandala - Healing Ragas  Music for Relaxation, Sleep and  Beyond]",
        "Jacob David - Intet Forbi",
        "My Way _Piano_frank sinatra",
        "Erik Satie - Gymnopedie no.1",
        "BTS - Serendipity - Piano Cover",
        "Bach: Suite for Cello solo no 1 in G major-Prelude",
        "05 Great Fairy's Fountain Theme",
        "Fairy Tail Main Theme (Violin And Piano)   Taylor Davis And Lara",
        "Clair de Lune - Debussy",
        "Beethoven - Moonlight Sonata (FULL)",
        "Unravel - A Tokyo Ghoul Orchestration",
        "That Home + To Build A Home",
        "SCARLXRD - FADED (piano live)",
        "Dreams",
        "BTS - I NEED U",
        "The Cinematic Orchestra - Arrival Of The Birds & Transformation (Edited)",
        "a moment in stasis (nelward cover)",
        "Philip Wesley - Dark Night Of The Soul",
        "08 Love’s Sorrow (Piano Solo Version)",
        "[FULL] BTS (방탄소년단) 'DNA' - Piano Cover",
        "Johann Sebastian Bach - Pachelbel's Cannon in D major",
        "Spirited Away",
        "Through The Valley",
        "BAD BUNNY - AMOR FODA",
        "1-800-273-8255 (feat. Alessia Cara & Khalid)",
        "I spoke to the devil in miami, he said everything would be fine",
        "MAX - Lights Down Low",
        "Too Good At Goodbyes (Acoustic)",
        "Ed Sheeran - Perfect (Official Audio)",
        "skin",
        "Rewind",
        "XXXTENTACION - Depression And Obsession (Slow)",
        "JENNIFER - TRINIDAD CARDONA",
        "Promises-Jhene Aiko",
        "BAD BUNNY - AMORFODA (OFICIAL)",
        "Rafa Caro",
        "Attention",
        "7. Blue Side (Outro)",
        "Halsey - Bad At Love (Stripped)",
        "Paul Anka - Put Your Head On My Shoulder",
        "Jhene Aiko PROMISES",
        "XXXTENTACION - I Don't Understand This",
        "2. P.O.P (Piece Of Peace) Pt.1",
        "All Of Me",
        "Camila Cabello - Never Be The Same (KUST Remix)",
        "Selena Gomez & Marshmello – Wolves",
        "SUNFLOWER",
        "Adiós Amor",
        "Humo Trankilizante - Hijos De Leyva (Studio)",
        "Pumped Up Kicks",
        "Feeling Whitney",
        "Riptide",
        "I Dont Understand This - XXXTentacion (Snippet extended)",
        "BEST FRIEND",
        "Virlan Garcia- En Donde Esta Tu Amor(2017)",
        "a message to tina belcher feat. Fifty Grand",
        "Same Drugs",
        "Lover Is A Day",
        "Let It Go",
        "Noah Cyrus - Again ft. Xxxtentacion",
        "Sencillamente De Ti",
        "FIND ME (Intro)",
        "Lorde - Tennis Court (Flume Remix)",
        "Ocean Eyes",
        "The Weekend, Wicked Games",
        "Am I Wrong",
        "Photograph",
        "trippie redd - make a wish (slowed + reverb)",
        "Moon River",
        "DARK PLACE (demo)",
        "Passenger - Let Her Go",
        "XO",
        "Ed Sheeran - Perfect (Leroy Sanchez Cover)",
        "The Middle",
        "haunt u w/lil peep",
        "Sad Nigga Hours (Prod. 904TEZZO)",
        "Body Like A Back Road",
        "You Make It Easy",
        "Lil Boom x DBangz - Kimono (Prod. Lil Boom)",
        "gunna - Car Sick (feat. NAV) (Drip Season 3)",
        "Kane Brown - Heaven.  Josh Turner - Hometown Girl.  Brett Young - In Case you Didn't Know.  Old Dominion - No Such thing as a Broken Heart",
        "Kane Brown - What ifs.  Sam Hunt - House Party.  Thomas Rhett - Die a Happy Man.   Dierks Bently - Somewhere on a Beach",
        "Small Town Boy",
        "Break Up In A Small Town",
        "13. Down For You Ft Lil Peep",
        "DRAKO VULGAR - OVERLOAD  + NO LIE (LEVITATINGMAN X LOKO LOS)",
        "My Girl",
        "Dirt On My Boots",
        "Milf Next Door (Prod. 904TEZZO)",
        "All On Me",
        "Light It Up",
        "Boy",
        "Thomas Rhett - Marry Me.  Dan + Shay - Tequila (NEW).  Upchurch & Colt Ford - Shoulda Named it after me.  Old Dominion - Written in the Sand",
        "You Should Be Here",
        "cut myself w/lil peep",
        "Broken Halos",
        "Tequila",
        "Florida Georgia Line - Stay (Black Stone Cherry Cover)",
        "Florida Georgia Line \"Cruise\" NO NELLY",
        "Luke Bryan - Play It Again ((Krispy Country ReDrum))",
        "That's My Kind Of Night",
        "Heartache On The Dance Floor",
        "Kane Brown Heaven Dee Jay Silver Country Club VIP RADIO Edit 80 bpm",
        "Buy Me A Boat",
        "Drinkin' Problem",
        "Hometown Girl",
        "Chicken Fried",
        "Aaron Lewis - \"Country Boy\"",
        "Rascal Flatts - What Hurts The Most",
        "Supreme Patty & A.Millz - Watchin",
        "Wanna Be That Song",
        "Came Here to Forget",
        "Luke Combs - When it Rain, it Pours.  Luke Bryan - Light it Up.  Kenny Chesney - All the Pretty Girls.  Florida Georgia Line - Smooth",
        "Strip It Down",
        "3RACKS [PROD. BIGHEAD & DJFLIP]",
        "BlocBoy JB & Drake - Look Alive (Prod. Tay Keith)",
        "Middle of a Memory",
        "From the Ground Up",
        "Boys 'Round Here (feat. Pistol Annies & Friends)",
        "Upchurch  Rollin Stoned (Official Video)",
        "Nonfiction Story",
        "Caillou",
        "Panic Attacks (feat. Yoshi Flower)",
        'billy',
        'plug walk',
        'blocboy jb "look alive" ft. drake',
        'bounce out with that | 6ix9ine billy rondo keke gummo offset lil boat 2 blood walk',
        'hope',
        'outside today',
        'xxxtentacion - fuck love  (feat. trippie redd)',
        'keke (ft. fetty wap & a boogie wit da hoodie)',
        'stir fry',
        'lil skies ft. landon cube "red roses" (prod. @menohbeats)',
        'dark knight dummo (ft. travis scott)',
        'new freezer (feat. kendrick lamar)',
        'gummo [prod. pierre bourne]',
        'all girls are the same (prod. nick mira)',
        'nowadays (feat. landon cube)[prod. by cash money ap]',
        'roll in peace (feat. xxxtentacion)',
        'ybn nahmir -"rubbin off the paint" (prod. izak)',
        'joey bada$$ vs xxxtentacion - king\'s dead (freestyle)',
        'solar eclipse',
        'queen - medicine (official audio)',
        'smokepurpp & murda beatz - 123',
        'nbayoungboat (feat. youngboy never broke again)',
        'pick it up (feat. a$ap rocky) [prod. by fki1st and sosa808]',
        'uka uka [produced by: ozmusiqe]',
        'migos - ice tray (ft. lil yatchy) |    japan out now!   | lil boat 2',
        'rondo feat. tory lanez & young thug',
        'the way life goes (feat. oh wonder)',
        'trippie redd & tekashi69 - poles1469 [produced by: pierre bourne]',
        '66 (feat. trippie redd)',
        'kooda',
        'mine',
        'rockstar (feat. 21 savage)',
        'betrayed (bobbyjohnson)',
        'rich the kid ft. trippie redd - early morning trappin (prod. lab cook)',
        'i fall apart',
        'from the d to the a feat. lil yachty',
        'king\'s dead',
        'love scars/you hurt me [produced by: elliott trent] &lt;/3',
        'wanted you (feat. lil uzi vert)',
        'lust [prod. cashmoneyap]',
        'lil peep - star shopping (prod. kryptik)',
        'motorsport',
        'xxxtentacion - jocelyn flores',
        'war with us',
        'my dawg',
        'tm88 x southside x lil uzi vert - mood (prod by tm88, southside, supah mario)',
        'no smoke',
        'a ghetto christmas carol prod. ronny j',
        'tay-k - the rac (prod: s.diesel) [@djphattt exclusive] *video in description*',
        'i don\'t wanna do this anymore',
        'different colors ft. lil yachty (prod. mexikodro)',
        'boof pack (prod. royce david)',
        'nbayoungboat (feat. youngboy never broke again)',
        'right or wrong (feat. future)',
        '66 (feat. trippie redd)',
        'joey bada$$ vs xxxtentacion - king\'s dead (freestyle)',
        'japan',
        '그때 헤어지면 돼 by jk of bts',
        'freshman list',
        'boom! (feat. ugly god)',
        'blessings',
        'since when (w/ 21 savage)',
        'she ready (feat. pnb rock)',
        'baby daddy (feat. lil pump & offset)',
        'round n round (feat. sprite lee)',
        'mickey (feat. offset & lil baby)',
        'xxxtentacion - moonlight',
        'bodybag',
        'oops (feat. 2 chainz & k$upreme)',
        'talk to me nice (feat. quavo)',
        'count me in',
        'don\'t even trip (ft. moneybagg yo)',
        'self made',
        'rl grime - i wanna know (feat. daya)',
        'xxxtentacion - i dont even speak spanish lol',
        'don\'t leave me - bts',
        'get money bros. (feat. tee grizzley)',
        '@yungpinch - cloud 9 (prod. @linkhalfway)',
        'love me forever',
        'trust issues(prod. kenny beats)',
        'xxxtentacion - the remedy for a broken heart',
        'kasbo - over you (feat. frida sundemo)',
        'xxxtentacion - numb',
        'fwm',
        'xxxtentacion - going down!',
        'boombox cartel - moon love (ft. nessly)',
        'motivation',
        'xxxtentacion - $$$ ft. matt ox',
        'dontai x sad frosty - frick da haterz (ilovefriday & poudii diss track)',
        'do not disturb',
        'japan',
        'where i\'m at',
        'party favor x baauer - mdr',
        'das cap',
        'khalid & normani - love lies (jimmie x felix palmqvist remix)',
        'jeremy zucker ft. blackbear - talk is overrated (manila killa remix)',
        '25 lighters',
        'pop out (feat. jban$2turnt)',
        'xxxtentacion - alone part 3',
        'flex'
        ];
}
