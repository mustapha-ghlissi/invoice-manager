<?php
namespace InvoiceGenerator\Entity;

class InvoicePayment
{
  private $id;
	private $invoice;
  private $method;
	private $date;
	private $amount;
	private $publicnote;
	private $privatenote;


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

  public function getMethod(): ?int
  {
    return $this->method;
  }
  public function setMethod(int $method): void
  {
    $this->method = $method;
  }

	public function getDate()
	{
		return $this->date;
	}
	public function setDate(\DateTime $date): void
	{
		$this->date = $date;
	}

  public function getAmount(): ?float
	{
		return $this->amount;
	}
	public function setAmount(float $amount): void
	{
		$this->amount = $amount;
	}

  public function getPublicNote(): ?string
	{
		return $this->publicnote;
	}
	public function setPublicNote(string $publicNote): void
	{
		$this->publicnote = $publicNote;
	}

  public function getPrivateNote(): ?string
	{
		return $this->privatenote;
	}
	public function setPrivateNote(string $privateNote): void
	{
		$this->privatenote = $privateNote;
	}
}
