<?php
namespace InvoiceGenerator\Entity;

class InvoiceRow
{
  private $id;
	private $invoice;
	private $label;
	private $quantity;
	private $tax;
	private $unityprice;
	private $note;


  public function getId(): ?int
  {
    return $this->id;
  }

	public function getInvoice(): ?int
	{
		return $this->invoice;
	}
	public function setInvoice(int $invoice): void
	{
		$this->invoice = $invoice;
	}

  public function getLabel(): ?string
  {
    return $this->label;
  }
  public function setLabel(string $label): void
  {
    $this->label = $label;
  }

	public function getQuantity(): ?int
	{
		return $this->quantity;
	}
	public function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}

  public function getTax(): ?int
	{
		return $this->tax;
	}
	public function setTax(int $tax): void
	{
		$this->tax = $tax;
	}

  public function getUnityPrice(): ?float
	{
		return $this->unityprice;
	}
	public function setUnityPrice(float $unityPrice): void
	{
		$this->unityprice = $unityPrice;
	}

  public function getNote(): ?string
	{
		return $this->note;
	}
	public function setNote(string $note): void
	{
		$this->note = $note;
	}
}
