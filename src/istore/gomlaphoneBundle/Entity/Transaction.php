<?php

namespace istore\gomlaphoneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="transaction")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Supplier")
     * @ORM\JoinTable(name="supplier" , 
     *                joinColumns={@ORM\JoinColumn(name="transaction_supplier_id", 
     *                                         referencedColumnName="id")})
     */
    protected $transaction_supplier;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $transaction_date;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $transaction_total_due;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $transaction_total_paid;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $transaction_discount;
    
    /**
     * @ORM\ManyToOne(targetEntity="Store")
     * @ORM\JoinTable(name="store" , 
     *                joinColumns={@ORM\JoinColumn(name="transaction_store", 
     *                                         referencedColumnName="id")})
     */
    protected $transaction_store;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transaction_date
     *
     * @param \DateTime $transactionDate
     * @return Transaction
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transaction_date = $transactionDate;

        return $this;
    }

    /**
     * Get transaction_date
     *
     * @return \DateTime 
     */
    public function getTransactionDate()
    {
        return $this->transaction_date;
    }

    /**
     * Set transaction_total_due
     *
     * @param integer $transactionTotalDue
     * @return Transaction
     */
    public function setTransactionTotalDue($transactionTotalDue)
    {
        $this->transaction_total_due = $transactionTotalDue;

        return $this;
    }

    /**
     * Get transaction_total_due
     *
     * @return integer 
     */
    public function getTransactionTotalDue()
    {
        return $this->transaction_total_due;
    }

    /**
     * Set transaction_discount
     *
     * @param integer $transactionDiscount
     * @return Transaction
     */
    public function setTransactionDiscount($transactionDiscount)
    {
        $this->transaction_discount = $transactionDiscount;

        return $this;
    }

    /**
     * Get transaction_discount
     *
     * @return integer 
     */
    public function getTransactionDiscount()
    {
        return $this->transaction_discount;
    }

    /**
     * Set transaction_supplier
     *
     * @param \istore\gomlaphoneBundle\Entity\Supplier $transactionSupplier
     * @return Transaction
     */
    public function setTransactionSupplier(\istore\gomlaphoneBundle\Entity\Supplier $transactionSupplier = null)
    {
        $this->transaction_supplier = $transactionSupplier;

        return $this;
    }

    /**
     * Get transaction_supplier
     *
     * @return \istore\gomlaphoneBundle\Entity\Supplier 
     */
    public function getTransactionSupplier()
    {
        return $this->transaction_supplier;
    }

    /**
     * Set transaction_store
     *
     * @param \istore\gomlaphoneBundle\Entity\Store $transactionStore
     * @return Transaction
     */
    public function setTransactionStore(\istore\gomlaphoneBundle\Entity\Store $transactionStore = null)
    {
        $this->transaction_store = $transactionStore;

        return $this;
    }

    /**
     * Get transaction_store
     *
     * @return \istore\gomlaphoneBundle\Entity\Store 
     */
    public function getTransactionStore()
    {
        return $this->transaction_store;
    }

    /**
     * Set transaction_total_paid
     *
     * @param integer $transactionTotalPaid
     * @return Transaction
     */
    public function setTransactionTotalPaid($transactionTotalPaid)
    {
        $this->transaction_total_paid = $transactionTotalPaid;

        return $this;
    }

    /**
     * Get transaction_total_paid
     *
     * @return integer 
     */
    public function getTransactionTotalPaid()
    {
        return $this->transaction_total_paid;
    }
}
