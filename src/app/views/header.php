<?php
session_start();
// add new product
$class = 'btn btn-info m-3';
echo $this->tag->linkTo(
    ['product'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Add new product!'), 'class' => $class]
);
// show all products
echo $this->tag->linkTo(
    ['product/show'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Show all products!'), 'class' => $class]
);
// place new order
echo $this->tag->linkTo(
    ['order'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Place new Order!'), 'class' => $class]
);
// show all orders
echo $this->tag->linkTo(
    ['order/show'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Show all Order!'), 'class' => $class]
);
// settings
echo $this->tag->linkTo(
    ['setting'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Setting'), 'class' => $class]
);
// add component
echo $this->tag->linkTo(
    ['addcomponent'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Add Component!'), 'class' => $class]
);
// add role
echo $this->tag->linkTo(
    ['role'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Add New Role'), 'class' => $class]
);
// acl page
echo $this->tag->linkTo(
    ['aclpage/acl'.'?bearer='.$_SESSION['currUser'], $this->locale->_('Access Control'), 'class' => $class]
);
