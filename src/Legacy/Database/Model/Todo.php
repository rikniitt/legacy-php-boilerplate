<?php

namespace Legacy\Database\Model;

use Legacy\Database\Model;
use Doctrine\ORM\Mapping as ORM;
use Respect\Validation\Validator;

/**
 * @ORM\Entity
 * @ORM\Table(name="todo")
 */
class Todo extends Model
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
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

    protected function setValidationRules($validator)
    {
        $required = new Validator();
        $required->notEmpty();

        $validator->attribute('name', $required);
        $validator->attribute('content', $required);
    }

}
