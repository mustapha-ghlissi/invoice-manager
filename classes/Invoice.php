<?php
namespace InvoiceGenerator\Entity;

class Invoice
{
  private $id;
	private $repertory;
	private $quote;
	private $ref;
	private $startDate;
	private $endDate;
	private $status;
	private $publicnote;
	private $privatenote;


  public function getId(): ?int
  {
    return $this->id;
  }

	public function getRepertory(): ?int
	{
		return $this->repertory;
	}
	public function setRepertory(int $repertory): void
	{
		$this->repertory = $repertory;
	}

  public function getQuote(): ?int
  {
    return $this->quote;
  }
  public function setQuote(int $quote): void
  {
    $this->quote = $quote;
  }

	public function getRef(): ?string
	{
		return $this->ref;
	}
	public function setRef(string $ref): void
	{
		$this->ref = $ref;
	}

  public function getStartDate()
	{
		return $this->startDate;
	}
	public function setStartDate(\DateTime $startDate): void
	{
		$this->startDate = $startDate;
	}

  public function getEndDate()
	{
		return $this->endDate;
	}
	public function setEndDate(\DateTime $endDate): void
	{
		$this->endDate = $endDate;
	}

  public function getStatus(): ?int
	{
		return $this->status;
	}
	public function setStatus(int $status): void
	{
		$this->status = $status;
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
