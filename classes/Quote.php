<?php
namespace InvoiceGenerator\Entity;

class Quote
{
	private $id;
	private $repertory = 1;
	private $ref;
	private $creationDate;
	private $dueDate;
	private $status;
	private $amount;
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

	public function getRef(): ?string
	{
		return $this->ref;
	}
	public function setRef(string $ref): void
	{
		$this->ref = $ref;
	}

  public function getCreationDate()
	{
		return $this->creationDate;
	}
	public function setCreationDate(\DateTime $creationDate): void
	{
		$this->creationDate = $creationDate;
	}

  public function getDueDate()
	{
		return $this->dueDate;
	}
	public function setDueDate(\DateTime $dueDate): void
	{
		$this->dueDate = $dueDate;
	}

  public function getStatus(): ?int
	{
		return $this->status;
	}
	public function setStatus(int $status): void
	{
		$this->status = $status;
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
	public function setPublicNote(string $publicnote): void
	{
		$this->publicnote = $publicnote;
	}

  public function getPrivateNote(): ?string
	{
		return $this->privatenote;
	}
	public function setPrivateNote(string $privatenote): void
	{
		$this->privatenote = $privatenote;
	}
}
