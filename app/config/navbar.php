<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'start'  => [
            'text'  => 'Start',
            'url'   => $this->di->get('url')->create(''),
            'title' => ''
        ],

        // This is a menu item
        'frågor' => [
            'text'  =>'Frågor',
            'url'   => $this->di->get('url')->create('questions/show-all'),
            'title' => ''
        ],
				
				// This is a menu item
        'tags' => [
            'text'  =>'Taggar',
            'url'   => $this->di->get('url')->create('tags/show-all'),
            'title' => ''
        ],
				
				// This is a menu item
        'users' => [
            'text'  =>'Användare',
            'url'   => $this->di->get('url')->create('users/show-all'),
            'title' => ''
        ],
				
				// This is a menu item
        'about' => [
            'text'  =>'Om sidan',
            'url'   => $this->di->get('url')->create('about'),
            'title' => ''
        ],
				
				// This is a menu item
        'login' => [
            'text'  =>'Logga in',
            'url'   => $this->di->get('url')->create('account/login'),
            'title' => '',
						'loginremove' => '1'
        ],
				
			  // This is a menu item
        'register' => [
            'text'  =>'Skapa konto',
            'url'   => $this->di->get('url')->create('account/register'),
            'title' => '',
						'loginremove' => '1'
        ],
				
				// This is a menu item
        'user' => [
            'text'  =>'Mitt konto',
            'url'   => $this->di->get('url')->create('account'),
            'title' => '',
						'login' => '1'
        ],
				
			  // This is a menu item
        'logout' => [
            'text'  =>'Logga ut',
            'url'   => $this->di->get('url')->create('account/logout'),
            'title' => '',
						'login' => '1'
        ],
    ],
 


    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($this->di->get('request')->getCurrentUrl($url) == $this->di->get('url')->create($url)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];
