<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Media;
use App\Models\MediaImage;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create 3 demo vendor users ────────────────────────────────────────
        $vendorUsers = [
            [
                'name'     => 'Pinnacle Media Group',
                'email'    => 'pinnacle@dodo.demo',
                'phone'    => '9800001111',
                'agency'   => 'Pinnacle Media Group',
            ],
            [
                'name'     => 'BrandSphere Outdoor',
                'email'    => 'brandsphere@dodo.demo',
                'phone'    => '9800002222',
                'agency'   => 'BrandSphere Outdoor',
            ],
            [
                'name'     => 'Nexus Digital Ads',
                'email'    => 'nexus@dodo.demo',
                'phone'    => '9800003333',
                'agency'   => 'Nexus Digital Ads',
            ],
        ];

        $vendors = [];
        foreach ($vendorUsers as $vu) {
            $user = User::firstOrCreate(
                ['email' => $vu['email']],
                [
                    'name'     => $vu['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'customer',
                ]
            );
            $vendor = Vendor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'agency_name' => $vu['agency'],
                    'phone'       => $vu['phone'],
                    'status'      => 'approved',
                ]
            );
            $vendors[] = $vendor;
        }

        // ── Media listings per type (5+ each) ────────────────────────────────
        // Format: [title, media_type, city, location, size, description, base_price, pricing_type, image_urls[]]

        $listings = [

            // ── BILLBOARD / OOH ─────────────────────────────────────────────
            ['Mumbai Billboard - Andheri Flyover',         'billboard', 'Mumbai',    'Andheri Flyover, Western Express Highway', '40×20 ft', 'Massive hoarding at one of Mumbai\'s busiest flyovers. 2.5 lakh daily impressions.',                              45000,  'time', ['https://images.unsplash.com/photo-1516912481808-3406841bd33c?w=800&h=500&fit=crop', 'https://images.unsplash.com/photo-1555685812-4b943f1cb0eb?w=800&h=500&fit=crop']],
            ['Delhi Billboard - Connaught Place',          'billboard', 'Delhi',     'Connaught Place Inner Circle',             '30×15 ft', 'Premium hoarding in the heart of Delhi\'s business district. High footfall 24/7.',                               55000,  'time', ['https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop']],
            ['Bangalore Hoarding - MG Road',               'billboard', 'Bangalore', 'MG Road, Near Brigade Road Junction',      '40×20 ft', 'Unobstructed view at Bangalore\'s prime commercial corridor.',                                                   38000,  'time', ['https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=800&h=500&fit=crop']],
            ['Chennai Hoarding - Anna Salai',              'billboard', 'Chennai',   'Anna Salai, Near LIC Building',            '30×10 ft', 'Iconic stretch of Chennai\'s main artery with heavy two-way traffic.',                                             30000,  'time', ['https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800&h=500&fit=crop']],
            ['Pune Hoarding - FC Road',                    'billboard', 'Pune',      'FC Road, Near Deccan Gymkhana',            '20×10 ft', 'Popular youth hangout area. High footfall from colleges and restaurants.',                                          22000,  'time', ['https://images.unsplash.com/photo-1486325212027-8081e485255e?w=800&h=500&fit=crop']],
            ['Hyderabad Billboard - HITECH City',          'billboard', 'Hyderabad', 'HITECH City Road, Madhapur',               '50×20 ft', 'Giant billboard in front of Hyderabad\'s IT corridor. Reaches 500+ tech professionals daily.',                     60000,  'time', ['https://images.unsplash.com/photo-1508739773434-c26b3d09e071?w=800&h=500&fit=crop']],
            ['Kolkata Hoarding - Park Street',             'billboard', 'Kolkata',   'Park Street, Near Flurys',                 '30×15 ft', 'Heritage location with upscale audiences. Prime for premium brand advertising.',                                    28000,  'time', ['https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=800&h=500&fit=crop']],

            // ── LED / DIGITAL SCREENS ────────────────────────────────────────
            ['LED Screen - Bandra-Kurla Complex',          'LED',       'Mumbai',    'BKC, Near Jio World Centre',               '20×12 ft', 'High-res P4 outdoor LED in Mumbai\'s premium financial district. 15-second slots available.',                      75000,  'time', ['https://images.unsplash.com/photo-1567016376408-0226e4d0c1ea?w=800&h=500&fit=crop']],
            ['LED Wall - Cyber Hub Gurgaon',               'LED',       'Delhi',     'Cyber Hub, DLF Phase 2, Gurgaon',          '24×14 ft', 'Vibrant LED display in Delhi NCR\'s trendiest F&B and nightlife hub.',                                             85000,  'time', ['https://images.unsplash.com/photo-1561489413-985b06da5bee?w=800&h=500&fit=crop']],
            ['LED Billboard - MG Road Pune',               'LED',       'Pune',      'MG Road Near Ruby Hall Clinic',            '18×10 ft', 'Bright digital display seen from 300m. Loop of 8 advertisers, 10s per slot.',                                        42000,  'time', ['https://images.unsplash.com/photo-1493612276216-ee3925520721?w=800&h=500&fit=crop']],
            ['LED Pole Kiosk - Brigade Road',              'LED',       'Bangalore', 'Brigade Road, Opposite McDonalds',         '6×3 ft',   'Compact but impactful digital pole display at Bangalore\'s most crowded pedestrian street.',                         18000,  'unit', ['https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=800&h=500&fit=crop']],
            ['LED Screen - Anna Nagar Tower',              'LED',       'Chennai',   'Anna Nagar Tower, 2nd Avenue',             '16×10 ft', 'Landmark digital display at Chennai\'s busy upscale residential area.',                                              35000,  'time', ['https://images.unsplash.com/photo-1579403124614-197f69d8187b?w=800&h=500&fit=crop']],
            ['LED Hoarding - Banjara Hills',               'LED',       'Hyderabad', 'Road No. 12, Banjara Hills',               '24×12 ft', 'Premium LED at Hyderabad\'s most affluent neighbourhood. Reaches HNI audience.',                                     65000,  'time', ['https://images.unsplash.com/photo-1542219550-37153d387c27?w=800&h=500&fit=crop']],

            // ── MALL ─────────────────────────────────────────────────────────
            ['Atrium Display - Phoenix Marketcity Mumbai', 'mall',      'Mumbai',    'Phoenix Marketcity, Kurla',                '10×8 ft',  'Central atrium display seen by all shoppers. 1.5L+ weekend footfall.',                                               90000,  'time', ['https://images.unsplash.com/photo-1519567241046-7f570eee3ce6?w=800&h=500&fit=crop']],
            ['Entry Gate Branding - Select Citywalk',      'mall',      'Delhi',     'Select Citywalk, Saket',                   '30×12 ft', 'Impossible to miss entry gate branding at Delhi\'s premium mall.',                                                   120000, 'time', ['https://images.unsplash.com/photo-1481437156560-3205f6a55735?w=800&h=500&fit=crop']],
            ['Food Court LED - Phoenix Palladium',         'mall',      'Mumbai',    'Phoenix Palladium, Lower Parel',           '8×5 ft',   'Three screens in the food court. Captive audience during meals. Avg dwell time 30 min.',                              55000,  'time', ['https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=800&h=500&fit=crop']],
            ['Kiosk Activation - Nexus Mall Pune',         'mall',      'Pune',      'Nexus Mall, Koregaon Park',                '12×12 ft', 'Central kiosk space for product demos, sampling and activations.',                                                   40000,  'time', ['https://images.unsplash.com/photo-1567958451986-2de427a4a0be?w=800&h=500&fit=crop']],
            ['Mall Facade Banner - Mantri Square',         'mall',      'Bangalore', 'Mantri Square Mall, Malleshwaram',         '60×20 ft', 'Massive exterior facade banner visible from the Malleswaram flyover.',                                               80000,  'time', ['https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=500&fit=crop']],
            ['Lift Branding - Lulu Mall Kochi',            'mall',      'Hyderabad', 'LuLu Mall, Hyderabad',                     'Full lift wrap', 'Brand all 8 lifts inside LuLu Mall. High dwell time, captive audience.',                                       35000,  'unit', ['https://images.unsplash.com/photo-1561715276-a2d087060f1d?w=800&h=500&fit=crop']],

            // ── AIRPORT ──────────────────────────────────────────────────────
            ['Terminal 2 Lightbox - CSMI Airport',         'airport',   'Mumbai',    'CSIA Terminal 2, Departure Check-in',      '6×4 ft',   'Backlit lightbox at T2 check-in zone. 25,000 pax daily. Pan-India affluent audience.',                               150000, 'time', ['https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=800&h=500&fit=crop']],
            ['Security Hold Area LED - IGI Airport',       'airport',   'Delhi',     'IGI Airport T3, Domestic Departure Lounge','16×9 ft',  'Dominant digital screen in the security hold area. Average wait time 45 min. Captive HNI audience.',                 200000, 'time', ['https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=800&h=500&fit=crop']],
            ['Baggage Belt Wrap - Kempegowda Airport',     'airport',   'Bangalore', 'BLR Airport, Arrivals Baggage Hall',       'Full belt', ' 100% unavoidable branding on the baggage belt. Avg viewing time 12 min.',                                           80000,  'time', ['https://images.unsplash.com/photo-1559494007-9f5847c49d94?w=800&h=500&fit=crop']],
            ['Aerobridge Branding - Chennai Airport',      'airport',   'Chennai',   'Chennai International, T1 Aerobridges',    'Full wrap', 'Aerobridge wrap seen up-close by every arriving and departing passenger.',                                            60000,  'time', ['https://images.unsplash.com/photo-1573608284468-54a5c8df9843?w=800&h=500&fit=crop']],
            ['Trolley Branding - Hyderabad Airport',       'airport',   'Hyderabad', 'Rajiv Gandhi International Airport',       'Trolley set','All 500+ baggage trolleys branded. Impressions throughout the terminal.',                                            45000,  'unit', ['https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800&h=500&fit=crop']],
            ['Premium Lounge Screen - Pune Airport',       'airport',   'Pune',      'Pune International Airport, Business Lounge','10×6 ft','Exclusive screen inside the business lounge. Reaches C-suite travellers.',                                            70000,  'time', ['https://images.unsplash.com/photo-1607462109225-6b64ae2dd3cb?w=800&h=500&fit=crop']],

            // ── TRANSIT ──────────────────────────────────────────────────────
            ['Metro Train Wrap - Mumbai Metro Line 1',     'transit',   'Mumbai',    'Mumbai Metro Line 1, Versova-Andheri-Ghatkopar','Full train','Full exterior train wrap. 4 lakh daily ridership.',                               120000, 'time', ['https://images.unsplash.com/photo-1556075798-4825dfaaf498?w=800&h=500&fit=crop']],
            ['Bus Shelter Branding - MH Roadways',        'transit',   'Mumbai',    '50 Bus Shelters across Mumbai',            '6×4 ft each','Premium bus shelter branding across Mumbai. High-impact neighborhood coverage.',                                     85000,  'time', ['https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=800&h=500&fit=crop']],
            ['Delhi Metro Station Domination',             'transit',   'Delhi',     'Rajiv Chowk Metro Station',                'Full station','Complete station domination with pillars, walls and floor graphics.',                                               250000, 'time', ['https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800&h=500&fit=crop']],
            ['Auto Hood Branding - Bangalore',             'transit',   'Bangalore', '500 Autos across Bangalore',               'Hood panel', 'Mobile advertising on 500 auto-rickshaws across Bangalore.',                                                        30000,  'unit', ['https://images.unsplash.com/photo-1570125909517-53cb21c89ff2?w=800&h=500&fit=crop']],
            ['Cab Branding - OLA/Uber Fleet',              'transit',   'Hyderabad', 'Hyderabad City-wide',                      'Door/bonnet','Branded OLA/Uber fleet with GPS tracking reports. 300 cabs, 30-day campaign.',                                       55000,  'time', ['https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800&h=500&fit=crop']],

            // ── CINEMA ───────────────────────────────────────────────────────
            ['PVR Gold Class 30s Spot - Mumbai',          'cinema',    'Mumbai',    'PVR Gold, Phoenix Marketcity',             '30 sec TVC', '30-second TVC slot before movies in PVR Gold class. Premium audience. 10 screens.',                                 40000,  'unit', ['https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=800&h=500&fit=crop']],
            ['INOX Scroll Branding - Delhi',              'cinema',    'Delhi',     'INOX, Select Citywalk',                    'Lobby scroll','Floor-to-ceiling scroll branding in the main lobby. 3 lakh monthly footfall.',                                      65000,  'time', ['https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?w=800&h=500&fit=crop']],
            ['Ticket Sleeve Branding - PAN India',        'cinema',    'Mumbai',    'All PVR Cinemas, PAN India',               'Ticket sleeve','Brand printed on 10 lakh ticket sleeves across PVR multiplexes across India.',                                    130000, 'unit', ['https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&h=500&fit=crop']],
            ['Cinema Branding - Cinepolis Bangalore',     'cinema',    'Bangalore', 'Cinepolis, Orion Mall',                    'Full lobby', 'Complete lobby takeover at Cinepolis. Concession counter branding + digital displays.',                               75000,  'time', ['https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=800&h=500&fit=crop']],
            ['Food Tray Branding - Hyderabad Cinemas',    'cinema',    'Hyderabad', 'PVR & INOX, All Hyderabad Screens',        'Tray mat',   'Branded food trays at every screen in Hyderabad. 2 lakh monthly impressions.',                                       25000,  'unit', ['https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=800&h=500&fit=crop']],

            // ── PRINT MEDIA ──────────────────────────────────────────────────
            ['TOI Mumbai Front Page Strip',               'print',     'Mumbai',    'Times of India, Mumbai Edition',           'Front page bottom strip', 'Front page strip ad in TOI Mumbai. 8 lakh+ daily circulation.',                                          180000, 'unit', ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=500&fit=crop']],
            ['HT Delhi Half Page Ad',                     'print',     'Delhi',     'Hindustan Times, Delhi Edition',           'Half page',  'Half-page ad in HT Delhi. Prime readership of 6.5 lakh daily.',                                                      120000, 'unit', ['https://images.unsplash.com/photo-1586339949216-35c2747cc36d?w=800&h=500&fit=crop']],
            ['Forbes India Full Page',                    'print',     'Mumbai',    'Forbes India Magazine',                    'Full page',  'Premium full-page ad in Forbes India. Reaches C-suite and HNI audience nationwide.',                                  250000, 'unit', ['https://images.unsplash.com/photo-1585776245991-cf89dd7fc73a?w=800&h=500&fit=crop']],
            ['Femina Magazine Back Cover',                'print',     'Mumbai',    'Femina Magazine - PAN India',              'Back cover', 'Back cover of India\'s leading women\'s magazine. 4.5 lakh circulation.',                                              200000, 'unit', ['https://images.unsplash.com/photo-1512314889357-e157c22f938d?w=800&h=500&fit=crop']],
            ['Deccan Herald Bangalore Insert',            'print',     'Bangalore', 'Deccan Herald, Bangalore Edition',         'A4 leaflet insert', 'A4 leaflet insert in Deccan Herald Bangalore. 2 lakh households.',                                             35000,  'unit', ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=500&fit=crop']],

            // ── DIGITAL ──────────────────────────────────────────────────────
            ['Instagram Creator Campaign - Tier 1',       'digital',   'Mumbai',    'PAN India',                                'Social media','Network of 50 Instagram creators (100K-500K followers). Reel + Story package.',                                     150000, 'unit', ['https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=800&h=500&fit=crop']],
            ['Google Display Network Campaign',           'digital',   'Mumbai',    'PAN India',                                '728×90, 300×250','Managed Google Display Network campaign. 10 million impressions guaranteed.',                                       85000,  'cpm',  ['https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=500&fit=crop']],
            ['YouTube Pre-Roll - Skippable 15s',          'digital',   'Mumbai',    'PAN India',                                '15s video',  'Skippable pre-roll ads on YouTube. Targeting by age, interest and geography.',                                          70000,  'cpm',  ['https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?w=800&h=500&fit=crop']],
            ['LinkedIn Sponsored Content',                'digital',   'Delhi',     'PAN India (B2B Focus)',                    'Sponsored post','Sponsored content on LinkedIn reaching India\'s professional audience.',                                             95000,  'cpm',  ['https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=800&h=500&fit=crop']],
            ['Programmatic Display - Premium Publishers', 'digital',   'Mumbai',    'PAN India',                                'Multi-format','Programmatic display across TOI, HT, Mint and 200+ premium publishers.',                                             60000,  'cpm',  ['https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=500&fit=crop']],

            // ── RADIO ────────────────────────────────────────────────────────
            ['Radio Mirchi Mumbai - RJ Mention',          'radio',     'Mumbai',    'Radio Mirchi 98.3 FM, Mumbai',             '60 sec spot','RJ mention + 60s jingle on Mumbai\'s #1 FM station. 8am-12pm prime slot.',                                           35000,  'unit', ['https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=800&h=500&fit=crop']],
            ['Red FM Delhi Power Hour Slot',              'radio',     'Delhi',     'Red FM 93.5, Delhi',                       '30 sec spot','Power hour slot on Red FM Delhi. Massive listenership 5pm-8pm evening drive.',                                        28000,  'unit', ['https://images.unsplash.com/photo-1485579149621-3123dd979885?w=800&h=500&fit=crop']],
            ['Big FM Bangalore Morning Drive',            'radio',     'Bangalore', 'Big FM 92.7, Bangalore',                   '45 sec spot','Morning drive spot on Big FM Bangalore. Premium time slot 7am-10am.',                                                  22000,  'unit', ['https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?w=800&h=500&fit=crop']],
            ['Fever FM Nationwide Campaign',              'radio',     'Mumbai',    'Fever FM - 8 Cities',                      '30 sec spot (×8 cities)','Multi-city radio campaign across Fever FM. Simultaneous broadcast in 8 metros.',                            200000, 'unit', ['https://images.unsplash.com/photo-1547104442-044448722bf0?w=800&h=500&fit=crop']],
            ['RJ Testimonial - Mirchi Pune',              'radio',     'Pune',      'Radio Mirchi 98.3 FM, Pune',               'Live RJ mention','Live RJ personal testimonial on Mirchi Pune. Authentic, conversational endorsement.',                               18000,  'unit', ['https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=800&h=500&fit=crop']],

            // ── TV ───────────────────────────────────────────────────────────
            ['STAR Plus Prime Time 30s Spot',             'tv',        'Mumbai',    'STAR Plus, All India',                     '30 sec TVC', 'Prime time slot on STAR Plus 8pm-10pm. India\'s #1 GEC. 150 million viewers.',                                         500000, 'unit', ['https://images.unsplash.com/photo-1593359677879-a4bb92f4834c?w=800&h=500&fit=crop']],
            ['News18 India Breaking News Strip',          'tv',        'Delhi',     'News18 India - National',                  'L-band strip','L-band strip during primetime news on News18 India.',                                                                   80000,  'unit', ['https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=500&fit=crop']],
            ['Sony LIV OTT Pre-Roll',                     'tv',        'Mumbai',    'Sony LIV - PAN India',                     '30 sec pre-roll','Premium non-skippable pre-roll on Sony LIV. Reaches urban streaming audience.',                                       60000,  'cpm',  ['https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?w=800&h=500&fit=crop']],
            ['Colors TV Scroll L-Band',                   'tv',        'Mumbai',    'Colors TV, All India',                     'Scroll strip','Scroll strip on Colors TV during Bigg Boss prime time. Massive viewership.',                                            120000, 'unit', ['https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=800&h=500&fit=crop']],
            ['Zee News Prime Slot',                       'tv',        'Delhi',     'Zee News - All India',                     '30 sec TVC', 'Prime news show slot on Zee News around 7pm evening bulletin.',                                                         70000,  'unit', ['https://images.unsplash.com/photo-1593359677879-a4bb92f4834c?w=800&h=500&fit=crop']],

            // ── INFLUENCER ───────────────────────────────────────────────────
            ['Mega Influencer - Lifestyle (5M+ Followers)','influencer','Mumbai',   'Instagram / YouTube',                      'Long-form + reels','Mega influencer lifestyle category. 5M+ followers. Dedicated reel + story + YouTube vlog.',                      300000, 'unit', ['https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=800&h=500&fit=crop']],
            ['Macro Food Blogger Bundle',                 'influencer','Bangalore', 'Instagram',                                '5 × Reels',  'Bundle of 5 macro food bloggers (500K-1M followers). Ideal for F&B brands.',                                            180000, 'unit', ['https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=800&h=500&fit=crop']],
            ['Fitness Micro Influencer Pack (20 creators)','influencer','Delhi',    'Instagram',                                '20 × Story posts','20 micro fitness influencers (50K-200K). Authentic reach in Tier 1 cities.',                                       120000, 'unit', ['https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&h=500&fit=crop']],
            ['Travel Creator - YouTube + Blog',           'influencer','Mumbai',    'YouTube / Blog',                           'Full video + post','Full YouTube video review + dedicated blog post from a 1M+ travel creator.',                                       250000, 'unit', ['https://images.unsplash.com/photo-1488085061387-422e29b40080?w=800&h=500&fit=crop']],
            ['Meme Page Campaign (5 Pages)',              'influencer','Delhi',      'Instagram',                               '5 × Meme posts','5 viral meme pages (1M-3M followers each). Fastest organic reach format.',                                           90000,  'unit', ['https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=800&h=500&fit=crop']],

            // ── SPORTS ───────────────────────────────────────────────────────
            ['IPL Match Boundary Boards - Mumbai Indians', 'sports',   'Mumbai',    'Wankhede Stadium',                         'Boundary board set','Set of 4 boundary boards at Wankhede for 1 IPL home match. 22,000 capacity.',                                   800000, 'unit', ['https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=800&h=500&fit=crop']],
            ['ISL Jersey Sponsorship - Right Chest',      'sports',    'Mumbai',    'Mumbai City FC',                           'Right chest logo','Right chest logo on all ISL match jerseys. Full ISL season.',                                                     1200000,'time', ['https://images.unsplash.com/photo-1522778119026-d647f0596c20?w=800&h=500&fit=crop']],
            ['Kabaddi League Branding - Banner Set',      'sports',    'Delhi',     'Dabur HCI Kabaddi League',                 'Backdrop + banners','Full set of backdrops and courtside banners for PKL home games.',                                               180000, 'time', ['https://images.unsplash.com/photo-1577223625816-7546f13df25d?w=800&h=500&fit=crop']],
            ['Badminton Tournament Title Sponsorship',    'sports',    'Hyderabad', 'Hyderabad Indoor Stadium',                 'Title sponsor package','Title sponsorship of a national-level badminton tournament. 5000+ attendees.',                               350000, 'unit', ['https://images.unsplash.com/photo-1622279457486-62dcc4a431d6?w=800&h=500&fit=crop']],
            ['Marathon Branding Package - Mumbai',        'sports',    'Mumbai',    'Mumbai Marathon Route',                    'Banners + BIB','Bib sponsorship + 20 banners on marathon route. 40,000 runners, 2 lakh spectators.',                                   250000, 'unit', ['https://images.unsplash.com/photo-1530549387789-4c1017266635?w=800&h=500&fit=crop']],
        ];

        $vendorCount = count($vendors);

        foreach ($listings as $i => $data) {
            [$title, $type, $city, $location, $size, $desc, $price, $pricing, $images] = $data;

            $vendor = $vendors[$i % $vendorCount];

            $media = Media::updateOrCreate(
                ['title' => $title, 'vendor_id' => $vendor->id],
                [
                    'vendor_id'    => $vendor->id,
                    'media_type'   => $type,
                    'city'         => $city,
                    'location'     => $location,
                    'size'         => $size,
                    'description'  => $desc,
                    'base_price'   => $price,
                    'pricing_type' => $pricing,
                    'price_on_call'=> false,
                    'status'       => 'active',
                ]
            );

            // Add images if not already there
            if ($media->images()->count() === 0) {
                foreach ($images as $url) {
                    MediaImage::create([
                        'media_id'  => $media->id,
                        'image_url' => $url,
                    ]);
                }
            }
        }

        if ($this->command) {
            $this->command->info('✅ Seeded ' . count($listings) . ' media listings across ' . $vendorCount . ' vendors.');
        }
    }
}
