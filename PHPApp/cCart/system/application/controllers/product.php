<?php

class Product extends Controller {

    public function  __construct()
    {
        parent::Controller();
        $this->load->library('cart');
    }

    public function index()
    {
        $this->load->view('product/index');
    }

    public function ext_get_all()
    {
        $query = $this->db->get('products');
        $product_arr = array();
        foreach($query->result() as $key => $data)
        {
            $product_arr[] = array(
                'id'    => $data->id,
                'name'  => $data->name,
                'price' => $data->price,
                'image' => $data->image
            );
        }
        echo json_encode(array('products' => $product_arr));
    }

    public function ext_get_cart()
    {
        if ($this->cart->total_items() != 0)
        {
            foreach($this->cart->contents() as $product)
            {
                $cart_arr[] = array(
                    'rowid' => $product['rowid'],
                    'id'    => $product['id'],
                    'qty'   => $product['qty'],
                    'name'  => $product['name'],
                    'price' => $product['price'],
                    'subtotal' => $product['subtotal']
                );
            }
            $cart_arr[] = array(
                'rowid' => '',
                'id'    => '',
                'qty'   => '',
                'name'  => '',
                'price' => '<b>Total:</b>',
                'subtotal' => '<b>'.$this->cart->total().'</b>'
            );
            echo json_encode(array('cart' => $cart_arr));
        }
        else
        {
            $cart_arr[] = array();
            echo json_encode(array('cart' => $cart_arr));
        }
    }

    public function ext_add_cart()
    {
        if ($_POST['rowid'] == '')
        {
            $data = array(
                'id'    => $_POST['id'],
                'qty'   => 1,
                'price' => $_POST['price'],
                'name'  => $_POST['name']
            );
            $this->cart->insert($data);
        }
        else
        {
            $data = array(
              'rowid'   => $_POST['rowid'],
              'qty'     => intval($_POST['qty']) + 1
            );
            $this->cart->update($data);
        }
        echo '{success:true, total: "'.$this->cart->total().'"}';
    }

    public function ext_update_cart()
    {
        $data = array(
          'rowid'   => $_POST['rowid'],
          'qty'     => $_POST['qty']
        );
        $this->cart->update($data);
        echo '{success:true}';
    }

    public function ext_clear_cart()
    {
        $this->cart->destroy();
        echo '{success:true}';
    }

}