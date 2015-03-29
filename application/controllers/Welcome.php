<?php

/**
 * Our homepage. Show the most recently added quote.
 *
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct()
    {
	    parent::__construct();
    }

    //-------------------------------------------------------------
    //  Homepage: show a list of the orders on file
    //-------------------------------------------------------------

    function index()
    {
    	// Build a list of orders
      $map = directory_map('./data/', 1);

      $test = '.xml';
      $menu = 'menu.xml';
      $data = array();
      foreach($map as $str)
      {
        if(substr_compare($str, $test, strlen($str)-strlen($test), strlen($test)) === 0)
        {
            if(strcmp($str, $menu))
            {
              $filename = array(
                'filename' => $str,
                'title' =>  substr((string)$str,0,-4). " (".getCustomer($str).")"
              );
              array_push($data, $filename);
            }
        }
      }
      $this->data['orders'] = $data;
    	// Present the list to choose from
    	$this->data['pagebody'] = 'homepage';
    	$this->render();
    }

    //-------------------------------------------------------------
    //  Show the "receipt" for a specific order
    //-------------------------------------------------------------

    function order($filename)
    {
    	// Build a receipt for the chosen order
      $this->load->model('order');

      $orderRAW = new Order($filename);
      $order = $orderRAW->getOrder();
      //var_dump($order->getOrder());

      $this->data['filename'] = $filename;
      $this->data['customer'] = $orderRAW->getCustomer();
      $this->data['specialOrder'] = $orderRAW->getSpecial();

      $orderArray = array();
      $oTotal = 0.00;

      $burgercount = 0;
      foreach($order as $burger)
      {
        $burgercount++;
        $burgerData = array(
          'order' => $filename,
          'base' => $burger->patty,
          'top' => implode(", ",$burger->topping),
          'sauce' => implode(", ",$burger->sauce)
        );

        //Check if sauce is empty
        if(empty($burger->sauce))
        {
          $burgerData['sauce'] = "None";
        }
        //Check if topping is empty
        if(empty($burger->topping))
        {
          $burgerData['top'] = "None";
        }

        //Only show cheese if there are cheese
        if($burger->cheeseT != NULL)
        {
          $burgerData['cheeseT'] = "Cheese (Top): ".$burger->cheeseT;
        }
        else
        {
          $burgerData['cheeseT'] = "";
        }
        if($burger->cheeseB != NULL)
        {
          $burgerData['cheeseB'] = "Cheese (Bottom): ".$burger->cheeseB;
        }
        else
        {
          $burgerData['cheeseB'] = "";
        }

        //set burger count
        $burgerData['burgercount'] = $burgercount;

        $burgerData['bTotal'] = $burger->bTotal;
        $oTotal += $burger->bTotal;
        //push to orderArray
        array_push($orderArray, $burgerData);
      }

      $this->data['order'] = $orderArray;
      $this->data['oTotal'] = $oTotal;
    	$this->data['pagebody'] = 'justone';
    	$this->render();
    }
}

function getCustomer($filename)
{
  $xml = simplexml_load_file(DATAPATH . $filename);

  $customer = (string) $xml->customer;

  return $customer;
}
