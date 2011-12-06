<?php
namespace Rodger\GalleryBundle\DoctrineExtension;

use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions;


class Greatest extends Functions\FunctionNode {

  protected $firstExpression, $secondExpression;
  
  public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
  {
    return sprintf("GREATEST(%s, %s)", 
            $this->firstExpression->dispatch($sqlWalker), 
            $this->secondExpression->dispatch($sqlWalker));
  }
  
  public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // (2)
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // (3)
        $this->firstExpression = $parser->ArithmeticPrimary(); // (4)
        $parser->match(Lexer::T_COMMA); // (5)
        $this->secondExpression = $parser->ArithmeticPrimary(); // (6)
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // (3)
    }
}
?>
