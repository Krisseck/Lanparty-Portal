<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class contains the method for interaction with the BinaryBeast API
 *
 * By using this API Class, you are agreeing to the Terms and Conditions
 * The terms can be found in the file "Terms.txt" included with this file
 * visit http://wiki.binarybeast.com/?title=BinaryBeast_API for more information
 * 
 * Example use, let's grab a list of countries that have the word 'king' in it, there are two ways to do it...
 *  $result = $bb->call('Country.CountrySearch.Search', array('country' => 'king'));
 *  $result = $bb->country_search('king');
 *      if($result->result == 200) foreach($result->countries as $country) {
 *          echo $country . '<br />';
 *      }
 *
 * @package BinaryBeast
 *
 * @author BinaryBeast.com
 *
 * @version 2.7.2 (2012-04-01)
 * 
 * Warning: XML and CSV do not work yet, JSON only
 *
 * Cannot be called statically, $bb->call('Service.Example.FTW', $args);
 *
 * For a list of available services, please see http://wiki.binarybeast.com/?title=BinaryBeast_API#Packages
 */
class BinaryBeast
{
    /**
     * Which method this server can use to call the BinaryBeast API
     * @access private
     * @var string: CURL|FOPEN
     */
    private $method;
    /**
     * Which return type to request
     * @access private
     * @var string: JSON|XML|CSV
     */
    private $return = null;
    /**
     * BinaryBeast API Key
     * @access private
     * @var string
     */
    private $api_key = '';
    /**
     * Login email cache
     */
    private $email    = null;
    private $password = null;
    /**
     * If the ssl verification causes issues, developers can disable it
     */
    private $verify_ssl = true;

    /**
     * A few constants to make a few values a bit easier to read / use
     */
    const API_VERSION                   = '2.7.2';
    //
    const BRACKET_GROUPS    = 0;
    const BRACKET_WINNERS   = 1;
    const BRACKET_LOSERS    = 2;
    const BRACKET_FINALS    = 3;
    const BRACKET_BRONZE    = 4;
    //
    const ELIMINATION_SINGLE    = 1;
    const ELIMINATION_DOUBLE    = 2;
    //
    const TOURNEY_TYPE_BRACKETS  = 0;
    const TOURNEY_TYPE_CUP       = 1;
    //
    const SEEDING_RANDOM        = 'random';
    const SEEDING_SPORTS        = 'sports';
    const SEEDING_BALANCED      = 'balanced';
    const SEEDING_MANUAL        = 'manual';
    //
    const REPLAY_DOWNLOADS_DISABLED         = 0;
    const REPLAY_DOWNLOADS_ENABLED          = 1;
    const REPLAY_DOWNLOADS_POST_COMPLETE    = 2;
    //
    const REPLAY_UPLOADS_DISABLED           = 0;
    const REPLAY_UPLOADS_OPTIONAL           = 1;
    const REPLAY_UPLOADS_MANDATORY          = 2;

    /**
     * Constructor - Sets Preferences
     *
     * The constructor can be passed an array of config values
     */
    public function __construct($config = array())
    {
        if (count($config) > 0)
        {
            $this->initialize($config);
        }

        $this->init_return();
        $this->init_method();
    }

    // --------------------------------------------------------------------

    /**
     * Initialize preferences
     *
     * @access  public
     * @param   array
     * @return  void
     */
    function initialize($config = array())
    {
        foreach ($config as $key => $val)
        {
            if (isset($this->$key))
            {
                $this->$key = $val;
            }
        }

    }


    /**
     * Alternative method of authentication - allow them to use a simple email / password combination
     * 
     * @param string $Email
     * @param string $Password
     * 
     * @return void
     */
    public function login($email, $password)
    {
        $this->email    = $email;
        $this->password = $password;
    }

    /**
     * If SSL Host verification causes any issues, call this method to disable it
     * @return void
     */
    public function disable_ssl_verification()
    {
        $this->verify_ssl = false;
    }

    /**
     * Re-enable ssl verification
     * @return void
     */
    public function enable_ssl_verification()
    {
        $this->verify_ssl = true;
    }


    /**
     * Unlike Call, this returns a raw un processed value, a return type can be specified
     * - which overrides whatever setting was automatically determined by the constructor
     *
     * It can be useful if your PHP can't handle JSON, but you want to use it
     * to feed json results to a local ajax request or something similiar
     *
     * Otherwise, it's just used internally so ignore it lol
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_call_raw
     *
     * @param @see BinaryBeast::Call()
     *
     * @return string
     */
    public function call_raw($svc, $args = null, $return_type = null)
    {
        //This server does not support curl or fopen
        if(!$this->method) return $this->get_method_error();

        //Add the service to the arguments, and the return type
        $args['api_return']  = is_null($return_type) ? $this->return : $return_type;
        $args['api_service'] = $svc;

        //Use a more readable snake_case argument/variable format
        //BinaryBeast uses CamelCase at its own back-end, and it's too late to change that now,
        //but it's easy to translate between camelCase and snake_case
        $args['api_use_underscores'] = 1;

        //Authenticate ourselves
        if(!is_null($this->api_key))
        {
            $args['api_key'] = $this->api_key;
        }

        //Though, it will always be supported back-end, the future releases of the api class wil not include this ability, and
        //use of the api_key will be encouraged
        if(!is_null($this->email))
        {
            $args['api_email']    = $this->email;
            $args['api_password'] = $this->password;
        }

        //Determine which function to call to retrieve the data
        $method = 'call_' . $this->method;

        //Who you gonna call?
        return $this->$method( http_build_query($args) );
    }

    /**
     * Make a service call to the remote BinaryBeast API
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_call
     *
     * @param string Service to call (ie Tourney.TourneyCreate.Create)
     * @param array  Arguments to send
     *
     * @return int result
     */
    public function call($svc, $args = null)
    {
        //This server does not support curl or fopen
        if(!$this->method) return $this->get_method_error();

        //Determine which method is needed to parse the returned value
        $method = 'get_' . $this->return;

        //Return a parsed value of call_raw
        return $this->$method( $this->call_raw($svc, $args) );
    }

    /**
     * Determines which method to use for call BinaryBeast with
     *
     * @return boolean false if the server supports neither fopen nor curl
     */
    private function init_method()
    {
        //cURL
        if(function_exists('curl_version')) $this->method = 'curl';

        //FOpen
        else if(ini_get('allow_url_fopen')) $this->method = 'fopen';

        //Failure!
        else $this->method = false;

        //Return true or false
        return !($this->method == false);
    }

    /**
     * Simply returns an array with a Result to return in case no method could be determined
     *
     * @return object {int result, string Message}
     */
    private function get_method_error()
    {
        return array('result'  => false,
             'message' => 'Neither cURL nor fopen is enabled on your server, the BinaryBeast API could not be contacted.  Get in touch with a BinaryBeast admin (contact@binarybeast.com) and they will be happy to help you'
         );
    }

    /**
     * Determines which return type to request, according to the local servce's abilities
     * Preferences json, then xml, then csv
     *
     * @return void - even the most uptight servers can parse a csv lol
     */
    private function init_return()
    {
        //Hopefully we can just use json.. it's so clean and easy
        if(function_exists('json_decode')) $this->return = 'json';

        //I doubt this will happen, but you never know - we MIGHT have to fall back on XML
        else $this->return = 'xml';

        /**
         * @TODO Check for xml suport, then fallback on CSV
         *
         * CSV is going to be next to useless, I'll have to
         * hard code the results into the wrapper functions
         *
         * which means using CSV, users will NOT be able to call services
         * unless I've written wrappers methods for them
         */
    }

    /**
     * Make a service call to the BinaryBeast API via the cURL library
     *
     * @access private
     *
     * @param string URL encoded arguments
     *
     * @return object[int result...]
     */
    private function call_curl($args)
    {
        //Get a curl instance
        $curl = curl_init();

        //Set the standard curl options
        curl_setopt($curl, CURLOPT_URL, 'https://binarybeast.com/api');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->verify_ssl ? 2 : 0);
        //
        curl_setopt($curl, CURLOPT_POST, true );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
        //
        curl_setopt($curl, CURLOPT_USERAGENT, 'BinaryBeast API PHP: Version ' . self::API_VERSION);

        //Execute, and return a parsed result
        $result = curl_exec($curl);

        //SSL Verification failed
        if(!$result)
        {
            return json_encode(array('result' => curl_errno($curl), 'error_message' => curl_error($curl)));
        }

        //Success!
        return $result;
    }

    /**
     * Make a service call to the BinaryBeast API via fopen
     *
     * @access private
     *
     * @param string $args URL encoded arguments
     *
     * @return object {int result}
     */
    private function call_fopen($args)
    {
        //Easy enough eh?
        return file_get_contents("https://binarybeast.com/api/?$args");
    }

    /**
     * Converts a returned JSON value into an object
     *
     * @param string BinaryBeast Result value
     *
     * @return object
     */
    private function get_json($result)
    {
        return (object)json_decode($result);
    }

    /**
     * Converts a returned XML value into an object
     *
     * @param string BinaryBeast Result value
     *
     * @return array
     */
    private function get_xml($result)
    {
        /**
         * @TODO figure this method out heh
         */
    }

    /**
     * Converts a returned CSV into an object
     *
     * @todo parsing CSV's... it's going to suck trying to have the return values be compatible with xml and json
     *
     * @param string BinaryBeast Result value
     *
     * @return object
     */
    private function get_csv($result)
    {
        return explode(',', str_replace('\n', ',', $result));
    }

    /**
     * This method is used to keep return types consistent
     * if an object is returned, it will be converted into an array
     *
     * This is needed because CSV and XML will return arrays natively... While json_decode will likely return an object
     *
     * This is not recursive though, many returns will contain objcts
     * For instance, a list of tournaments is an array of objects
     *
     * @param array $array Object to convert into an array
     *
     * @return object
     */
    private function array_to_object($array)
    {
        //No need to continue
        if(!is_array($array)) return $array;
        
        //EZ
        return (object)$array;
    }








    /**
     *
     * 
     * 
     * 
     * Tournament wrapper methods
     * 
     * 
     * 
     *
     */

    /**
     * Returns an object containing information about the given tournament
     * 
     * @param string $tourney_id 
     * 
     * @return {object}
     */
    public function tournament_load($tourney_id)
    {
        return $this->call('Tourney.TourneyLoad.Info', array('tourney_id' => $tourney_id));
    }

    /**
     * Returns an object containing teams for the given tournament
     * 
     * @param string $tourney_id 
     * 
     * @return {object}
     */
    public function tournament_load_teams($tourney_id)
    {
        return $this->call('Tourney.TourneyLoad.Teams', array('tourney_id' => $tourney_id));
    }
    
    /**
     * Retrieves round format
     * 
     * You can pass '*' for the bracket to retrieve for the entire tournament
     * 
     * @param int $bracket 
     * 
     * @return {object}
     */
    public function tournament_load_round_format($tourney_id, $bracket = '*')
    {
        return $this->call('Tourney.TourneyLoad.Rounds', array('tourney_id' => $tourney_id, 'bracket' => $bracket));
    }


    /**
     * This wrapper method is a shortcut to create a tournament, it simply calls the Tourney.TourneyCreate.Create service
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_create
     * 
     * Available options, check the wiki for their meanings, and default / possible values: 
     *      string title
     *      string description
     *      int    public
     *      string game_code            (SC2, BW, QL examples, @see http://wiki.binarybeast.com/index.php?title=API_PHP:_game_search)
     *      int    type_id              (0 = elimination brackets, 1 = group rounds to elimination brackets)
     *      int    elimination          (1 = single, 2 = double
     *      int    max_teams
     *      int    team_mode            (id est 1 = 1v1, 2 = 2v2)
     *      int    group_count
     *      int    teams_from_group
     *      date   date_start
     *      string location
     *      array  teams
     *      int    return_data          (0 = TourneyID and URL only, 1 = List of team id's inserted (from teams array), 2 = team id's and full tourney info dump)
     * 
     * @param array $options        keyed array of options
     *
     * @return object {int result, ...}
     */
    public function tournament_create($options)
    {
        //Use the legacy parameter method
        //@todo this method is deprecated and should be phased out eventually
        if(!is_array($options) && !is_null($options))
        {
            $keys = array('title' => 'PHP Test!', 'description' => null, 'public' => true, 'game_code' => null, 'type_id' => 0, 'elimination' => 1
                , 'max_teams' => 16, 'team_mode' => 1, 'teams_from_group' => 2, 'date_start' => null, 'location' => null, 'teams' => null, 'return_data' => 0);
            $pos = 0;
            foreach($keys as $key => $default)
            {
                ${$key} = func_get_arg($pos);
                if(${$key} === false) ${$key} = $default;
                ++$pos;
            }
            return $this->tournament_create_legacy($title, $description, $public, $game_code, $type_id, $elimination, $max_teams, $team_mode, $teams_from_group, $date_start, $location, $teams, $return_data);
        }

        //EZ
        return $this->call('Tourney.TourneyCreate.Create', $options);
    }
    /**
     * Created to preserve existing PHP applications that may decide to download the new class
     * 
     * The new method of calling complex services is to provide an associative array of options
     * previously we had defined each option as a parameter - so in case they're still 
     * using the old method, we'll make sure it still works
     * 
     * @param string $title
     * @param string $description
     * @param int $public
     * @param string $game_code
     * @param int $type_id
     * @param int $elimination
     * @param int $max_teams
     * @param int $team_mode
     * @param int $teams_from_group
     * @param date $date_start YYYY-MM-DD HH:SS
     * @param string $location
     * @param array $teams
     * @param type $return_data
     * 
     * @return {object} 
     */
    private function tournament_create_legacy($title, $description = null, $public = 1, $game_code = null, $type_id = 0, $elimination = 1, $max_teams = 16, $team_mode = 1, $teams_from_group = 2, $date_start = null, $location = null, array $teams = null, $return_data = 0)
    {
        $args = array(
            'title' => $title
            , 'description'         => $description
            , 'public'              => $public
            , 'game_code'           => $game_code
            , 'type_id'             => $type_id
            , 'elimination'         => $elimination
            , 'max_teams'           => $max_teams
            , 'team_mode'           => $team_mode
            , 'group_count'         => $group_count
            , 'teams_from_group'    => $teams_from_group
            , 'date_start'          => $date_start
            , 'location'            => $location
            , 'teams'               => $teams
            , 'return_data'         => $return_data
        );

        return $this->call('Tourney.TourneyCreate.Create', $args);
    }

    /**
     * This wrappper method will update the settings of a tournament (Tourney.TourneyUpdate.Settings)
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_update
     *
     * Available options, check the wiki for their meanings, and default / possible values: 
     *      string title
     *      string description
     *      int    public
     *      string game_code            (SC2, BW, QL examples, @see http://wiki.binarybeast.com/index.php?title=API_PHP:_game_search)
     *      int    type_id              (0 = elimination brackets, 1 = group rounds to elimination brackets)
     *      int    elimination          (1 = single, 2 = double
     *      int    max_teams
     *      int    team_mode            (id est 1 = 1v1, 2 = 2v2)
     *      int    group_count
     *      int    teams_from_group
     *      date   date_start
     *      string location
     *      array  teams
     *      int    return_data          (0 = TourneyID and URL only, 1 = List of team id's inserted (from teams array), 2 = team id's and full tourney info dump)
     * 
     * @param string $tourney_id
     * @param array $options        keyed array of options
     *
     * @return object {int result}
     */
    public function tournament_update($tourney_id, $options = array())
    {
        //Use the old legacy method of defining every parameter
        if(!is_array($options) && !is_null($options))
        {
            $keys = array('title', 'description', 'public', 'game_code', 'type_id', 'elimination', 'max_teams', 'team_mode', 'teams_from_group', 'date_start', 'location');
            foreach($keys as $x => $key)
            {
                ${$key} = func_get_arg($x + 1);
                if(${$key} === false) ${$key} = 'null';
            }
            return $this->tournament_update_legacy($tourney_id, $title, $description, $public, $game_code, $type_id, $elimination, $max_teams, $team_mode, $teams_from_group, $date_start, $location);
        }

        $args = array_merge(array('TourneyID'	=> $tourney_id), $options);
        return $this->call('Tourney.TourneyUpdate.Settings', $args);
    }
    /**
     * Created to allow existing applications to download this new class without breaking their application
     * 
     * The new method of calling complicated services is to pass in an associative array of arguments
     * 
     * @param type $tourney_id
     * @param type $title
     * @param type $description
     * @param type $public
     * @param type $game_code
     * @param type $type_id
     * @param type $elimination
     * @param type $max_teams
     * @param type $team_mode
     * @param type $teams_from_group
     * @param type $date_start
     * @param type $location
     * @return {object} 
     */
    private function tournament_update_legacy($tourney_id, $title, $description = 'null', $public = 'null', $game_code = 'null', $type_id = 'null', $elimination = 'null', $max_teams = 'null', $team_mode = 'null', $teams_from_group = 'null', $date_start = 'null', $location = 'null')
    {
        $args = array(
            'TourneyID'                 => $tourney_id
            , 'title' 			=> $title
            , 'description'		=> $description
            , 'public' 		 	=> $public
            , 'game_code'	 	=> $game_code
            , 'type_id'        		=> $type_id
            , 'elimination'	 	=> $elimination
            , 'max_teams'      		=> $max_teams
            , 'team_mode'      		=> $team_mode
            , 'teams_from_group'  	=> $teams_from_group
            , 'date_start'		=> $date_start
            , 'location'      		=> $location
        );
        return $this->call('Tourney.TourneyUpdate.Settings', $args);
    }

    /**
     * This wrapper method will delete a tournament (Tourney.TourneyDelete.Delete)
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_delete
     *
     * @param string $tourney_id		Obviously we need to know which tournament to delete
     *
     * @return object {int result}
     */
    public function tournament_delete($tourney_id)
    {
        return $this->call('Tourney.TourneyDelete.Delete', array('tourney_id' => $tourney_id));
    }

    /**
     * This wrapper class is a shortcut to Tourney.TourneyStart.Start
     * It will generate groups or brackets, depending on TypeID and Status
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_start
     *
     * @param string       $tourney_id		Obviously, we need to know which tournament to start
     * @param string       $seeding           See the help page on what these mean [random, traditional, balanced, manual]
     * @param array        $teams             If seeding is anything but random, you'll need to provide an ordered array of tourney_team_ids, either in order of team position, or rank
     *
     * @return object {int result}
     */
    public function tournament_start($tourney_id, $seeding = 'random', $teams = null)
    {
        $args = array('tourney_id' => $tourney_id
            , 'Seeding' => $seeding
            , 'Teams'   => $teams
        );

        return $this->call('Tourney.TourneyStart.Start', $args);
    }
    
    /**
     * Change the format of a round within a tournament (best of, map, and date)
     * 
     * This function also works to create the details - even if they have not yet been provided
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_round_update
     * 
     * @param string    $tourney_id
     * @param int       $bracket      - which bracket the round effects - ie 0 = groups, 1 = winners (there are class constants for these values
     * @param int       $round        - which round to update, starting with 0 for the first round
     * @param int       $best_of      - just like it sounds, BO$X - pass in the interger 1 for Best of 1
     * @param string    $map
     * @param string    $date
     * 
     * @return object {int result}
     */
    public function tournament_round_update($tourney_id, $bracket, $round = 0, $best_of = 1, $map = null, $date = null)
    {
        return $this->call('Tourney.TourneyRound.Update', array(
            'tourney_id'    => $tourney_id
            , 'bracket'     => $bracket
            , 'round'       => $round
            , 'best_of'     => $best_of
            , 'map'         => $map
            , 'date'        => $date
        ));
    }

    /**
     * the round_update function is fine and all.. but not incredibly effecient for large tournaments with many rounds and brackets, this method
     * allows you to update all rounds with one call, by passing in a simple array
     * 
     * @see link http://wiki.binarybeast.com/index.php?title=API_PHP:_tournament_round_update_batch
     * 
     * @param string         $tourney_id
     * @param int           $bracket      - which bracket the round effects - ie 0 = groups, 1 = winners (there are class constants for these values
     * @param <int>array    $best_ofs     - array of best_of values to update, IN ORDER ($best_ofs[0] = round 1, $best_ofs[1] = round 2)
     * @param <string>array $maps         - array of maps for this bracket
     * @param <string>array $dates        - array of dates for this bracket
     * @param <int>array    $map_ids      - array of map_ids - official maps with stat tracking etc in our databased, opposed to simply trying to give us the name of the map - use map_list to get maps ids
     * 
     */
    public function tournament_round_update_batch($tourney_id, $bracket, $best_ofs = array(), $maps = array(), $dates = array(), $map_ids = array())
    {
        return $this->call('Tourney.TourneyRound.BatchUpdate', array(
            'tourney_id'    => $tourney_id
            , 'bracket'     => $bracket
            , 'best_ofs'    => $best_ofs
            , 'maps'        => $maps
            , 'map_ids'     => $map_ids
            , 'dates'       => $dates
        ));
    }

    /**
     * Old method, left for legacy API users 
     */
    public function tournament_list($filter = null, $limit = 30, $private = true)
    {
        return $this->tournament_list_my($filter, $limit, $private);
    }

    /**
     * Retrieves a list of tournaments created using your account
     * 
     * @param string $filter        Optionally, you may filter by title
     * @param int    $limit         Limit the number of results - defaults to 30
     * 
     * @return object
     */
    public function tournament_list_my($filter = null, $limit = 30, $private = true)
    {
        return $this->call('Tourney.TourneyList.Creator', array(
            'filter'    => $filter,
            'page_size' => $limit,
            'private'   => $private
        ));
    }

    /**
     * Retrieves a list of matches that have are currently opened 
     * This does not help you determine matches that are waiting on opponents, 
     * it simply lets you know currently open matches
     * 
     * @param string $tourney_id
     * 
     * @return object[int Result [, array matches]]
     */
    public function tournament_get_open_matches($tourney_id)
    {
        return $this->call('Tourney.TourneyLoad.OpenMatches', array('tourney_id' => $tourney_id));
    }

    /**
     * Reopen a tournament
     * 
     * Complete -> Active,Active-Brackets -> Active-Brackets -> Active-Groups, Active/Active-Groups -> Confirmation
     * 
     * @param string $tourney_id 
     */
    public function tournament_reopen($tourney_id)
    {
        return $this->call('Tourney.TourneyReopen.Reopen', array('tourney_id' => $tourney_id));
    }

    /**
     * Wrapper method for Tourney.TourneySetStatus.Confirmation, allow players to confirm their positions
     * 
     * @param string $tourney_id
     * 
     * @return object {int result]
     */
    public function tournament_confirm($tourney_id)
    {
        return $this->call('Tourney.TourneySetStatus.Confirmation', array('tourney_id' => $tourney_id));
    }

    /**
     * Wrapper method for Tourney.TourneySetStatus.Confirmation, allow players to confirm their positions
     * 
     * @param string $tourney_id
     * 
     * @return object {int result]
     */
    public function tournament_unconfirm($tourney_id)
    {
        return $this->call('Tourney.TourneySetStatus.Building', array('tourney_id' => $tourney_id));
    }


    /**
     *
     * 
     * 
     * 
     * Teams/Participants wrapper methods
     * 
     * 
     * 
     *
     */


    /**
     * This wrapper class will insert a team into your tournament (Tourney.TourneyTeam.Insert)
     * It will automatically confirm the team unless it has already been filled according to your MaxTeams setting
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_insert
     * 
     * Available options:
     *      string country_code
     *      int    status               (0 = unconfirmed, 1 = confirmed, -1 = banned)
     *      string notes                Notes on the team - this can also be used possibly to store a team's remote userid for your own website
     *      array  players              If the TeamMode is > 1, you can provide a list of players to add to this team, by CSV (Player1,,Player2,,Player3)
     *      string network_name         If the game you've chosen for the tournament has a network configured (like sc2 = bnet 2, sc2eu = bnet europe), you can provide their in-game name here
     * 
     * @param string $tourney_id    
     * @param string $display_name  The team / player name
     * @param array  $options        keyed array of options
     *
     * @return array [int result [, int tourney_team_id]]
     */
    public function team_insert($tourney_id, $display_name, $options = array())
    {
        $args = array_merge(array('tourney_id'   => $tourney_id
            , 'display_name'        => $display_name
            , 'status'              => 1
        ), $options);

        return $this->call('Tourney.TourneyTeam.Insert', $args);
    }
    
    /**
     * Change a team's settings
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_update
     * 
     * Available options, check the wiki for their meanings, and default / possible values: 
     *      string country_code
     *      int    status               (0 = unconfirmed, 1 = confirmed, -1 = banned)
     *      string notes                Notes on the team - this can also be used possibly to store a team's remote userid for your own website
     *      array  players              If the TeamMode is > 1, you can provide a list of players to add to this team, by CSV (Player1,,Player2,,Player3)
     *      string network_name         If the game you've chosen for the tournament has a network configured (like sc2 = bnet 2, sc2eu = bnet europe), you can provide their in-game name here
     *
     * @param type $tourney_team_id
     * @param type $options 
     * 
     * @return object {int result}
     */
    public function team_update($tourney_team_id, $options)
    {
        $args = array_merge(array('tourney_team_id'   => $tourney_team_id), $options);
        return $this->call('Tourney.TourneyTeam.Update', $args);
    }
    
    /**
     * Granted that the tournament can still accept new teams, this method will update the status of a team to confirm his position in the draw
     * 
     * Unless otherwise specified, if you manually add a team through team_insert, he is confirmed by default
     * 
     * btw here's a tip: you can actually pass in '*' for the tourney_team_id to confirm ALL teams, but you would also have to include $tourney_id
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_confirm
     * 
     * @param type $tourney_team_id 
     * 
     * @return object {int result [, int tourney_team_id]}
     */
    public function team_confirm($tourney_team_id, $tourney_id = null)
    {
        return $this->call('Tourney.TourneyTeam.Confirm', array('tourney_team_id' => $tourney_team_id, 'tourney_id' => $tourney_id));
    }

    /**
     * Granted that the tournament hasn't started yet, this method can be used to unconfirm a team, so he will no longer be included in the grid once the tournament starts
     * 
     * Unless otherwise specified, if you manually add a team through team_insert, he is confirmed by default
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_unconfirm
     * 
     * @param type $tourney_team_id 
     * 
     * @return object {int result [, int tourney_team_id]}
     */
    public function team_unconfirm($tourney_team_id)
    {
        return $this->call('Tourney.TourneyTeam.Unconfirm', array('tourney_team_id' => $tourney_team_id));
    }

    /**
     * BANNEDED!!!
     * 
     * Ban a team from the tournament
     * 
     * Warning: this will NOT work if the tournament has already started, the best you can do is rename the player (using team_update, 'display_name' => 'foo')
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_ban
     * 
     * @param type $tourney_team_id 
     * 
     * @return object {int result [, int tourney_team_id]}
     */
    public function team_ban($tourney_team_id)
    {
        return $this->call('Tourney.TourneyTeam.Ban', array('tourney_team_id' => $tourney_team_id));
    }

    /**
     * This wrapper method will delete a team from a touranment
     * as long as the tournament has not been started or the team is unconfirmed
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_delete
     *
     * @param int $tourney_team_id 	Which team to delete - the binarybeast value TourneyTeamID
     *
     * @return object {int result}
     */
    public function team_delete($tourney_team_id)
    {
        return $this->call('Tourney.TourneyTeam.Delete', array('tourney_team_id' => $tourney_team_id));
    }

    /**
     * This wrapper method will report a team's win (Tourney.TourneyTeam.ReportWin)
     * 
     * Available Options:
     *  @param int     `score`				The score of the winner
     *  @param int     `o_score`                        The score fo the opponent (loser)
     *  @param bool    `draw`                           If this match was a draw, pass in true for this value
     *  @param string  `replay`				A URL to download the replay (first match only, for more detailed replay per game within the match, see Tourney.TourneyGame services for b03+)
     *  @param string  `map`				You may specify which map it took place on (applies to the first match only, for more, see the Tourney.TourneyGame services)
     *  @param string  `notes`				An optional description of the match
     *  @param boolean `force`				You can pass in true for this paramater, to force advancing the team even if he has no opponent (it would have thrown an error otherwise)
     * 
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_report_win
     *
     * @param string    $tourney_id                     Duh
     * @param int       $winner_tourney_team_id         The winner's team id
     * @param int       $loser_tourney_team_id          Only used / necessary in group rounds, the TeamID of the loser
     * @param array     $options                        An associative array of additional options
     *
     * @return object {int result}
     */
    public function team_report_win($tourney_id, $winner_tourney_team_id, $loser_tourney_team_id = null, $options = array())
    {
        //The new way of processing this request allows an array of options, as opposed to 15,000 parameters
        //But there are many sites already using the old method, so we make sure not to break their applications
        if(!is_array($options) && !is_null($options))
        {
            $keys = array('score' => 1, 'o_score' => 0, 'replay' => null, 'map' => null, 'notes' => null, 'force' => 0);
            $pos = 3;
            foreach($keys as $key => $default)
            {
                ${$key} = func_get_arg($pos);
                if(${$key} === false) ${$key} = $default;
                ++$pos;
            }
            return $this->team_report_win_legacy($tourney_id, $winner_tourney_team_id, $loser_tourney_team_id, $score, $o_score, $replay, $map, $notes, $force);
        }

        $args = array_merge(array('tourney_id' => $tourney_id
            , 'tourney_team_id'	   => $winner_tourney_team_id
            , 'o_tourney_team_id'  => $loser_tourney_team_id
        ), $options);
        return $this->call('Tourney.TourneyTeam.ReportWin', $args);
    }
    /**
     * Legacy method allowing defining each paramater as opposed to the new method of defining an array of options 
     * Necessary since I upgraded team_report_win to use options instead of definint every single paramater
     * Since there are many sites already using it the PHP API, we're setting up a fallback in case they 
     * decide to grab the latest API class, it would be nice if it worked the same way
     */
    public function team_report_win_legacy($tourney_id, $tourney_team_id, $o_tourney_team_id = null,
            $score = 1, $o_score = 0, $replay = null, $map = null, $notes = null, $force = false)
    {
        $args = array('tourney_id' => $tourney_id
            , 'tourney_team_id'	   => $tourney_team_id
            , 'o_tourney_team_id'  => $o_tourney_team_id
            , 'score'		   => $score
            , 'o_score'		   => $o_score
            , 'replay'		   => $replay
            , 'map'		   => $map
            , 'notes'		   => $notes
            , 'force'		   => $force
        );

        return $this->call('Tourney.TourneyTeam.ReportWin', $args);
    }

    /**
     * This wrapper will return the TourneyTeamID of the given team (Tourney.TourneyTeam.GetOTourneyTeamID)
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_team_get_opponent
     *
     * Note: a Result of 200 = the team currently has an opponent
     * Result 735 and OTourneyTeamID -1 = The team has been eliminated
     * Result 734 and OTourneyTeamID 0  = The team is currently waiting on an opponent
     *
     * Also, if the team has been eliminated, it will return an object 'Victor" with some information
     * about the winning team
     *
     * @param int $tourney_team_id		The team of which to determine the opponent
     *
     * @return object {int Result [, object TeamInfo, array Players]}
     */
    public function team_get_opponent($tourney_team_id)
    {
        return $this->call('Tourney.TourneyTeam.GetOTourneyTeamID', array('tourney_team_id' => $tourney_team_id));
    }
    
    /**
     * Returns as much information about a team as possible
     * 
     * @param int $tourney_team_id 
     * 
     * @return object {int result {
     */
    public function team_load($tourney_team_id)
    {
        return $this->call('Tourney.TourneyLoad.Team', array('tourney_team_id' => $tourney_team_id));
    }
    
    
    
    
    
    
    /**
     *
     * 
     * 
     * Match service wrappers
     * 
     * 
     *  
     */
    
    /**
     * Save the individual game details for a reported match
     * 
     * Each array (winners, scores, o_scores, races, maps, and replays) must be indexed in order of game
     * so winners[0] => is the tourney_team_id of the player that won the FIRST game in the match
     * winners[1] => the tourney_team_id of the player that won the second game... et c
     * 
     * 
     * It's important to note that scores refers to the score of the winner of that specific game
     * 
     * So if player 1 defeats player 2 30 to 17 in game one
     * Then let's say game 2.. player 2 defeats player 1 13:7...
     * 
     * In such a scenario, here's how your arrays should look:
     *  winners[0] => tourney_team_id of player 1
     *  score[0]   => score of player 1
     *  o_score[0] => score of player 2
     *  --
     *  winners[1] => tourney_team_id of player 2
     *  score[1]   => score of player 2
     *  o_score[1] => score of player 1
     * 
     * 
     * @param int $tourney_match_id
     * @param array $winners
     * @param array $scores
     * @param array $o_scores
     * @param array $maps
     * 
     * @return {object}
     */
    public function match_report_games($tourney_match_id, array $winners, array $scores, array $o_scores, array $maps)
    {
        $args = array(
            'tourney_match_id'      => $tourney_match_id,
            'winners'               => $winners,
            'scores'                => $scores,
            'o_scores'              => $o_scores,
            'races'                 => $races,
            'maps'                  => $maps,
        );
        return $this->call('Tourney.TourneyMatchGame.ReportBatch', $args);
    }


    /**
     *
     * 
     * 
     * 
     * Map wrapper methods
     * 
     * 
     * 
     *
     */
    
    /**
     * Load a list of maps for the given game_code
     * 
     * this is important to have in order for you to have the ability to 
     * specify maps for the round format for each bracket, as you can 
     * identify the maps by simply giving us the map_id
     * 
     * @param string $game_code
     * 
     * @return {object}
     */
    public function map_list($game_code)
    {
        return $this->call('Game.GameMap.LoadList', array('game_code' => $game_code));
    }



    /**
     *
     * 
     * 
     * 
     * Games wrapper methods
     * 
     * 
     * 
     *
     */


    
    /**
     * This wrapper will return a list of games according to the filter you provide
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_game_search
     *
     * Note: a Result of 601 means that your search term was too short, must be at least 3 characters long
     *
     * @param string $filter     filter the results with a generic filter
     *
     * @return object {int result [, array games]}
     */
    public function game_search($filter)
    {
        return $this->call('Game.GameSearch.Search', array('game' => $filter));
    }

    /**
     * This wrapper will return a list of games the most popular games at BinaryBeast
     *
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_game_list_top
     *
     * @param int $limit        simply limits the number of results, as this service is NOT paginated
     *
     * @return object {int result, [array games]}
     */
    public function game_list_top($limit)
    {
        return $this->call('Game.GameSearch.Top', array('limit' => $limit));
    }
    
    
    
    /**
     * 
     * 
     * 
     * Country services
     * 
     * 
     * 
     * 
     */
    
    
    /**
     * This wrapper allows you to search through the ISO list of countries
     * This is useful because BinaryBeast team's use ISO 3 character character codes, so 
     * to keep it simple, you can just look through our list of countries to get the codes
     * 
     * There's nothing special about our list of countries however, you can look up the official list on wikipedia
     * 
     * @see @link http://wiki.binarybeast.com/index.php?title=API_PHP:_country_search
     * 
     * @param string $country
     * 
     * @return object {int result, [array countries]}
     */
    public function country_search($country)
    {
        return $this->Call('Country.CountrySearch.Search', array('country' => $country));
    }
}

?>