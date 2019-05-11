<?php
/**
 * Created by PhpStorm.
 * User: Lobanov Kyryll
 * Date: 11.05.19
 * Time: 16:59
 */

/**
 * Use it for validate brackets into arithmetic expression
 */
class ValidateArithmeticExpression
{
    const CORRECT_ANSWER = 'Верно';
    const WRONG_ANSWER = 'Не верно';

    /**
     * Order have sense
     */
    public static $openedBrackets = ['{', '[', '('];
    public static $closedBrackets = ['}', ']', ')'];

    private $openedBracketsTypes;
    private $closedBracketsTypes;

    private $expression;

    /**
     * ValidateArithmeticExpression constructor.
     *
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        $this->expression       = $expression;
        $this->openedBracketsTypes = array_flip(self::$openedBrackets);
        $this->closedBracketsTypes = array_flip(self::$closedBrackets);
        $this->validate();
    }

    /**
     * Something like main function
     */
    private function validate()
    {
        try {
            $this->validateBracketsStructure();
            $this->validateBracketsUsage();

            echo $this->returnAnswer(true);
        } catch (Exception $e) {
            echo $this->returnAnswer(false);
        }
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    private function validateBracketsStructure(): bool
    {
        $openedBracketsCount = 0;
        $closedBracketsCount = 0;
        $brackets = [];

        $generator = $this->expressionGenerator($this->expression);

        foreach ($generator as $item) {
            $this->validationStructureHelper($item, self::$openedBrackets, $openedBracketsCount, $this->openedBracketsTypes, $brackets);
            $this->validationStructureHelper($item, self::$closedBrackets, $closedBracketsCount, $this->closedBracketsTypes, $brackets);

            /** If first bracket closed */
            if ($closedBracketsCount > $openedBracketsCount) {
                throw new Exception('Closed bracket before opened!');
            }
        }

        if ($openedBracketsCount !== $closedBracketsCount) {
            throw new Exception('Wrong brackets count!');
        }

        foreach ($brackets as $bracket) {
            if (0 !== ($bracket % 2)) {
                throw new Exception('Unpaired brackets!');
            }
        }

        return true;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    private function validateBracketsUsage(): bool
    {
        $order = [];

        $generator = $this->expressionGenerator($this->expression);

        foreach ($generator as $item) {
            if (in_array($item, self::$openedBrackets)) {
                $type = $this->openedBracketsTypes[$item];
                $order[] = $type;
            }

            if (in_array($item, self::$closedBrackets)) {
                $type = $this->closedBracketsTypes[$item];
                $correctOrder = array_reverse($order);
                $correctOrder = reset($correctOrder);
                array_pop($order);
                if ($type !== $correctOrder) {
                    throw new Exception('Invalid brackets usage!');
                }
            }
        }

        return true;
    }

    /**
     * @param $item
     * @param $types
     * @param $bracketsCount
     * @param $bracketTypes
     * @param $brackets
     */
    private function validationStructureHelper($item, $types, &$bracketsCount, $bracketTypes, &$brackets)
    {
        if (in_array($item, $types)) {
            ++$bracketsCount;
            $type = $bracketTypes[$item];
            if (!isset($brackets[$type])) {
                $brackets[$type] = 0;
            }
            ++$brackets[$type];
        }
    }

    /**
     * @param string $expression
     *
     * @return Generator
     */
    private function expressionGenerator(string $expression)
    {
        foreach (str_split($expression) as $item) {
            yield $item;
        }
    }

    /**
     * @param bool $answer
     *
     * @return string
     */
    private function returnAnswer(bool $answer): string
    {
        if (false === $answer) {
            return self::WRONG_ANSWER."\n";
        }

        return self::CORRECT_ANSWER."\n";
    }
}

/** Examples of usage */
new ValidateArithmeticExpression('[{((1+2)}{-(1/2))-{(2*5)}}]');
new ValidateArithmeticExpression('(1+2)(1/2){(2*5)}');
new ValidateArithmeticExpression('(1+2)[(1/2]){(2*5)}');
