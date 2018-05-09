<?php
namespace InvoiceGenerator\Entity;

class QuoteRow
{
  private $id;
	private $quote;
	private $label;
	private $quantity;
	private $tax;
	private $unityprice;
	private $note;


  public function getId(): ?int
  {
    return $this->id;
  }

	public function getQuote(): ?int
	{
		return $this->quote;
	}
	public function setQuote(int $quote): void
	{
		$this->quote = $quote;
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
	public function setUnityPrice(float $unityprice): void
	{
		$this->unityprice = $unityprice;
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
