<?php

namespace Legacy\Database\Model;

use Legacy\Database\Model;
use Doctrine\ORM\Mapping as ORM;

/**
 * Todo
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity
 */
class Todo extends Model
{

    /**
     * @var id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var name
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var content
     *
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    protected function validate()
    {
        // No validation for now.
    }

}
