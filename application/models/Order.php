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
            $bTotal = 0.00;
            $brgr = new stdClass();
            $brgr->patty = (string) $burger->patty['type'];
            if($menu->getPatty($brgr->patty) != NULL)
            {
              $bTotal += $menu->getPatty($brgr->patty)->price;
            }

            $brgr->cheeseT = (string) $burger->cheeses['top'];
            if($menu->getCheese($brgr->cheeseT) != NULL)
            {
              $bTotal += $menu->getCheese($brgr->cheeseT)->price;
            }

            $brgr->cheeseB = (string) $burger->cheeses['bottom'];
            if($menu->getCheese($brgr->cheeseB) != NULL)
            {
              $bTotal += $menu->getCheese($brgr->cheeseB)->price;
            }

            $brgr->topping = array();

            foreach($burger->topping as $toppings)
            {
              array_push($brgr->topping, $toppings['type']);
              if($menu->getTopping((string)$toppings['type']) != NULL)
              {
                $bTotal += $menu->getTopping((string)$toppings['type'])->price;
              }
            }

            $brgr->sauce = array();

            foreach($burger->sauce as $sauces)
            {
              array_push($brgr->sauce, $sauces['type']);
              if($menu->getSauce((string)$sauces['type']) != NULL)
              {
                $bTotal += $menu->getSauce((string)$sauces['type'])->price;
              }
            }

            $brgr->bTotal = $bTotal;
            array_push($this->order, $brgr);
        }
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
