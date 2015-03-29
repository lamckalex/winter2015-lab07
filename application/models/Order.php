<?php
class Order extends CI_Model {

    protected $xml = null;
    protected $order;
    protected $customer;
    protected $specialOrder;

    // Constructor
    public function __construct($filename = NULL) {
        parent::__construct();
        if($filename == NULL)
        {
          return;
        }

        $this->load->model('menu');
        $menu = new Menu();

        $this->xml = simplexml_load_file(DATAPATH . $filename);

        $this->customer = (string) $this->xml->customer;
        $this->specialOrder = (string) $this->xml['type'];

        $this->order = array();
        foreach ($this->xml->burger as $burger) {
            $brgr = new stdClass();
            $brgr->patty = (string) $burger->patty['type'];
          //  var_dump($menu->getPatty((string) $burger->patty['type']));
            $brgr->cheeseT = (string) $burger->cheeses['top'];
            $brgr->cheeseB = (string) $burger->cheeses['bottom'];
            $brgr->topping = (string) $burger->topping['type'];
            $brgr->sauce = array();

            foreach($burger->sauce as $sauces)
            {
              array_push($brgr->sauce, $sauces['type']);
            }

            array_push($this->order, $brgr);
        }
        //var_dump($order[0]);
    }

    function getOrder()
    {
        return $this->order;
    }

    function getCustomer()
    {
      return $this->customer;
    }

    function getSpecial()
    {
      return $this->specialOrder;
    }

    // retrieve a patty record, perhaps for pricing
    function getPatty($code) {
        if (isset($this->patties[$code]))
            return $this->patties[$code];
        else
            return null;
    }

}
