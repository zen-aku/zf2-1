<?php

namespace MysqlGenerator\Adapter\AdapterTrait;

/** 
 * 
 */
trait QuoteTrait {
    
    /**
     * Quote identifier
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * Quote identifier chain
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain) {
        $identifierChain = str_replace('`', '``', $identifierChain);
        if (is_array($identifierChain)) {
            $identifierChain = implode('`.`', $identifierChain);
        }
        return '`' . $identifierChain . '`';
    }
   
    /**
     * Quote value
     * @param  string $value
     * @return string
     */
    public function quoteValue($value) {        
        return $this->quote($value);      
    }
   
    /**
     * Quote identifier in fragment
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array()) {
        // regex taken from @link http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
        $parts = preg_split('#([^0-9,a-z,A-Z$_])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($safeWords) {
            $safeWords = array_flip($safeWords);
            $safeWords = array_change_key_case($safeWords, CASE_LOWER);
        }
        foreach ($parts as $i => $part) {
            if ($safeWords && isset($safeWords[strtolower($part)])) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '`' . str_replace('`', '``', $part) . '`';
            }
        }
        return implode('', $parts);
    }
        
}

