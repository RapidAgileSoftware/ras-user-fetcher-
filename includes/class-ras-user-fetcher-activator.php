<?php
/**
 * Fired during plugin activation
 *
 * @link       rasta.online
 * @since      1.0.0
 *
 * @package    Ras_User_Fetcher
 * @subpackage Ras_User_Fetcher/includes
 */


class Ras_User_Fetcher_Activator {

  // available endpoint
	protected $endpoint;
  // page title of userfetcher page
	protected $page_title ='User Fetcher';
  // reference for UserFetcher
  protected $page;
  // snippet to be included to start js functionalities
	protected $snippet = '<div id="ras_user_fetcher"><b>Start</b</div><script>console.log("Wicked snippet ...");</script>';

  public function __construct(string $endpoint) {
    $this->endpoint = $endpoint;
  }
	/**
	 * runs once while plugin activation
	 * adds required page if not already existent
	 *
	 * @since    1.0.0
	 */
	public function activate():bool
  {
		return $this->create_userfetcher_page();
	}

  /**
   * creates a user fetcher page as endpoint if not available yet
   *
   * @since    1.0.0
   */
	protected function create_userfetcher_page():bool
  {
    	if(!$this->userfetcher_exists()){
    		// create page for the user fetcher
    		$page_userfetcher = [
				  'post_title'  => $this->page_title,
				  'post_name'		=> $this->endpoint,
  				'post_content'=> $this->get_snippet(),
  				'post_status' => 'publish',
  				'post_author' => get_current_user_id(),
  				'post_type'   => 'page'
    		];
   			wp_insert_post( $page_userfetcher );
   			return true;
    	}
    	return false;
    }

	/**
	 * runs on plugin deactivation
	 * triggers deletion of userfetcher page
	 *
	 * @since    1.0.0
	 */
	public function deactivate():bool {
		return $this->delete_userfetcher_page();
	}

  /**
   * deletes a userfetcher page
   *
   * @since    1.0.0
   */
  protected function delete_userfetcher_page():bool
  {
    $page = $this->get_page();
    // nothing to clean up, early finish
    if($page === false){
        return false;
    }
    else{
      //delete our custom page
      wp_delete_post($page->ID);
      // we update Activator to forget the old page
      $this->page = false;
      // refresh post data
      wp_reset_postdata ();
      return true;
    }
  }

  /**
   * checks is there is already a userfetcher page
   *
   * @since    1.0.0
   */
  public function userfetcher_exists():bool
  {
    return ($this->get_page()===false)? false:true;
  }

  /**
   * delivers snippet text to be included
   *
   * @since    1.0.0
   */
  public function get_snippet():string {
    return $this->snippet;
  }

  public function get_page()
  {
    // check if we fetched the page already
    if(!isset($this->page)){
      // set the page or false if non-existent
      $this->page = get_page_by_path($this->endpoint)??false;
    }
    return $this->page;
  }

  public function set_endpoint(string $endpoint)
  {
    $this->endpoint=$endpoint;
    return $this;
  }

  public function set_snippet(string $snippet)
  {
    $this->snippet=$snippet;
    return $this;
  }

  public function set_page_title(string $page_title)
  {
    $this->page_title=$page_title;
    return $this;
  }

}
