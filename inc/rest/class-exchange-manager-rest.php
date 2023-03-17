<?php
# === To deny anyone access to this file directly
if (!defined('ABSPATH')) {
  die("Direct access forbidden");
}
# === Check if ExchangeManagerRest class is created
if (!class_exists('ExchangeManagerRest')) :
  # === Create ExchangeManagerRest class which interacts with the Model class to give data from the database
  class ExchangeManagerRest extends ExchangeManagerModel implements SingletonInterface
  {
    # === Create static instance of this class
    private static self $instance;
    # === Create a static method used to get instance once
    public static function getInstance(): self
    {
      # === Checks if instance has not been set
      if (!isset(self::$instance)) {
        # === set instance to new object class
        self::$instance = new self;
      }
      # === Return current object class
      return self::$instance;
    }
    # === Routes methods
    public function routes(): void
    {
      # === Get bank details
      # === https://wp.site.local/testing-server/wp-json/em/bank-details/get
      add_action('rest_api_init', [$this, 'getBankDetailsRoute']);
      # === update bank details
      # === https://wp.site.local/testing-server/wp-json/em/bank-details/update
      add_action('rest_api_init', [$this, 'updateBankDetailsRoute']);
      # === delete user account
      # === https://wp.site.local/testing-server/wp-json/em/users/delete?id={$id}
      add_action('rest_api_init', [$this, 'deleteUserRoute']);
      # === get top assets
      # === https://wp.site.local/testing-server/wp-json/em/top-assets/get
      add_action('rest_api_init', [$this, 'getTopAssetsRoute']);
      # === get transaction notification
      # === https://wp.site.local/testing-server/wp-json/em/transaction/get
      add_action('rest_api_init', [$this, 'getTransactionRoute']);
      # === get top news
      # === https://wp.site.local/testing-server/wp-json/em/top-news/get
      add_action('rest_api_init', [$this, 'getTopNewsRoute']);
      # === get support ticket
      # === https://wp.site.local/testing-server/wp-json/em/tickets/get
      add_action('rest_api_init', [$this, 'getSupportTicketRoute']);
      # === get single ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/get?id={$id}
      add_action('rest_api_init', [$this, 'getSingleSupportTicketRoute']);
      # === mark ticket as closed
      # === https://wp.site.local/testing-server/wp-json/em/ticket/close?id={$id}
      add_action('rest_api_init', [$this, 'closeSupportTicketRoute']);
      # === reopen ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/open?id={$id}
      add_action('rest_api_init', [$this, 'openSupportTicketRoute']);
      # === update last activity on ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/update/activity?id={$id}
      add_action('rest_api_init', [$this, 'updateTicketActivityRoute']);
      # === delete ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/delete?id={$id}
      add_action('rest_api_init', [$this, 'deleteTicketRoute']);
      # === get customer tickets
      # === https://wp.site.local/testing-server/wp-json/em/ticket/customer?id={$id}
      add_action('rest_api_init', [$this, 'getCustomerTicketsRoute']);
      # === get all chats on ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/chats/all?id={$id}
      add_action('rest_api_init', [$this, 'getAllChatsTicketsRoute']);
      # === get recent chats on ticket
      # === https://wp.site.local/testing-server/wp-json/em/ticket/chats/recent?id={$id}
      add_action('rest_api_init', [$this, 'getRecentChatsTicketsRoute']);
    }
    # === Customers permission
    public function permitCustomers(): bool
    {
      # === Get user
      $user = wp_get_current_user();
      # === Check if user is not a customer then return false
      if (!in_array("customer", $user->roles)) return false;
      # === If user is a customer return true
      return true;
    }
    # === Get bank details route
    public function getBankDetailsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getBankDetails'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'bank-details/get', $args);
    }
    # === Get bank details method
    public function getBankDetails()
    {
      # === Try block
      try {
        # === Get all customers
        $allBankDetails = $this->getCustomersBankDetails();
        # === Set customers data as response
        $response = new WP_REST_Response($allBankDetails);
        # === Set data status
        $response->set_status(200);
        # === Return response data
        return $response;
        # === Catch block
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'unknown error occured', # code
          'an unknown error occured while trying to fetch giftcards', # message
          array('status' => 400) # status
        );
      }
    }
    # === Update bank details route
    public function updateBankDetailsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'POST',
        'callback' => [$this, 'updateBankDetails'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'bank-details/update', $args);
    }
    # === Update bank details route
    public function updateBankDetails($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === Sanitize all present request
          $bankName = sanitize_text_field($request['bankName']);
          $bankAccountNumber = sanitize_text_field($request['bankAccountNumber']);
          $bankAccountName = sanitize_text_field($request['bankAccountName']);
          # === Turn request into array
          $details = array(
            'bankName' => $bankName,
            'bankAccountNumber' => $bankAccountNumber,
            'bankAccountName' => $bankAccountName
          );
          # === Customer id to change
          $where = ['id' => $request['id']];
          # === Update customer details
          $this->updateCustomerBankDetails($details, $where);
          # === Set response message
          $response = new WP_REST_Response('Bank details updated successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }
    # === Delete user route
    public function deleteUserRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'DELETE',
        'callback' => [$this, 'deleteUser'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'users/delete', $args);
    }
    # === Delete user method
    public function deleteUser($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === get user with matching id
          $user = get_user_by('id', $id);
          # === get user role
          $role = $user->roles;
          # === check if user role is not customer
          if ($role[0] !== 'customer') :
            # === Return error message
            return new WP_Error(
              'not customer', # code
              'This user is not a customer', # message
              array('status' => 400) # status
            );
          endif;
          # === delete user
          wp_delete_user($id);
          # === Set response message
          $response = new WP_REST_Response('user deleted successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }
    # === Top assets route
    public function getTopAssetsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getTopAssets'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'top-assets/get', $args);
    }
    # === Top assets method
    public function getTopAssets()
    {
      # === Try block
      try {
        # === Get all top assets
        $topAssets = $this->getAllTopAssets();
        # === Set response message
        $response = new WP_REST_Response($topAssets);
        # === Set response status
        $response->set_status(200);
        # === Return response
        return $response;
        # === Catch block
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }
    # === Get transactions route
    public function getTransactionRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getTransaction'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'transaction/get', $args);
    }
    # === Get transactions method
    public function getTransaction($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $userWallet = hid_ex_m_wallet_page_data($id);
          # === Get all transactions from user wallet
          $allTransactions = $userWallet['all_transactions'];
          # === Set response message
          $response = new WP_REST_Response($allTransactions);
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }
    # === Top assets route
    public function getTopNewsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getTopNews'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'top-news/get', $args);
    }
    # === Top assets method
    public function getTopNews()
    {
      # === Try block
      try {
        # === Get all top assets
        $allNews = $this->getAllTopNews();
        # === Set result array to be populated
        $results = [];
        # === Populate results array
        foreach ($allNews as $news) {
          $results[] = [
            "id" => $news->id,
            "title" => $news->title,
            "image" => wp_get_attachment_url($news->newsPicture),
            "date" => $news->dateAdded
          ];
        }
        # === Set response message
        $response = new WP_REST_Response($results);
        # === Set response status
        $response->set_status(200);
        # === Return response
        return $response;
        # === Catch block
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }
    # === Top assets route
    public function getSupportTicketRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getSupportTicket'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'tickets/get', $args);
    }
    # === Top assets method
    public function getSupportTicket()
    {
      # === Try block
      try {
        # === Get all Tickets
        $allTickets = hid_ex_m_get_all_support_tickets();
        # === Set response message
        $response = new WP_REST_Response($allTickets);
        # === Set response status
        $response->set_status(200);
        # === Return response
        return $response;
        # === Catch block
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'error processing order', # code
          "an error occured while trying to update bank details - $th", # data
          array('status' => 400) # status
        );
      }
    }

    public function getSingleSupportTicketRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getSingleSupportTicket'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/get', $args);
    }

    public function getSingleSupportTicket($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $ticket = hid_ex_m_get_single_ticket_data($id);
          # === Set response message
          $response = new WP_REST_Response($ticket);
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function closeSupportTicketRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'UPDATE',
        'callback' => [$this, 'closeSupportTicket'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/close', $args);
    }

    public function closeSupportTicket($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $where = ['id' => $id];
          hid_ex_m_mark_support_ticket_as_close($where);
          # === Set response message
          $response = new WP_REST_Response('Ticket closed successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function openSupportTicketRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'UPDATE',
        'callback' => [$this, 'openSupportTicket'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/open', $args);
    }

    public function openSupportTicket($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $where = ['id' => $id];
          hid_ex_m_reopen_support_ticket($where);
          # === Set response message
          $response = new WP_REST_Response('Ticket opened successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function updateTicketActivityRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'UPDATE',
        'callback' => [$this, 'updateTicketActivity'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/update/activity', $args);
    }

    public function updateTicketActivity($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          hid_ex_m_update_last_activity($id);
          # === Set response message
          $response = new WP_REST_Response('Ticket last activity updated successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function deleteTicketRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'DELETE',
        'callback' => [$this, 'deleteTicket'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/delete', $args);
    }

    public function deleteTicket($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          hid_ex_m_delete_support_ticket($id);
          # === Set response message
          $response = new WP_REST_Response('Ticket deleted successfully');
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function getCustomerTicketsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getCustomerTickets'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/customer', $args);
    }

    public function getCustomerTickets($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $tickets = hid_ex_m_get_customer_support_tickets($id);
          # === Set response message
          $response = new WP_REST_Response($tickets);
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function getRecentChatsTicketsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getRecentChatsTickets'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/chats/recent', $args);
    }

    public function getRecentChatsTickets($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          $time = $request->get_param('time');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          if (!$time) :
            # === Return error message
            return new WP_Error(
              'Time not found', # code
              'You forgot to attach the time', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $chats = hid_ex_m_get_recent_support_chat_data($time, $id);
          # === Set response message
          $response = new WP_REST_Response($chats);
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }

    public function getAllChatsTicketsRoute(): void
    {
      # === Arguments for route method
      $args = [
        'methods'  => 'GET',
        'callback' => [$this, 'getAllChatsTickets'],
        'permission_callback' => [$this, 'permitCustomers']
      ];
      # === Register route
      register_rest_route('em', 'ticket/chats/all', $args);
    }

    public function getAllChatsTickets($request)
    {
      # === Check if customer's id is present
      if (isset($request['id'])) :
        # === Try block
        try {
          # === get query param
          $id = $request->get_param('id');
          # === check if id is not a number
          if (!is_numeric($id)) :
            # === Return error message
            return new WP_Error(
              'id not a number', # code
              'This id you sent is not a numerical value', # message
              array('status' => 400) # status
            );
          endif;
          # === Get user wallet with matching id
          $chats = hid_ex_m_get_all_support_chat($id);
          # === Set response message
          $response = new WP_REST_Response($chats);
          # === Set response status
          $response->set_status(200);
          # === Return response
          return $response;
          # === Catch block
        } catch (\Throwable $th) {
          # === Return error message
          return new WP_Error(
            'error processing order', # code
            "an error occured while trying to update bank details - $th", # data
            array('status' => 400) # status
          );
        }
      else :
        # === Return error message
        return new WP_Error(
          'no request', # code
          'no request was submitted for process', # message
          array('status' => 400) # status
        );
      endif;
    }
  }
endif;
