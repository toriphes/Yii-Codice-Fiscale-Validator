<?php
/**
 * CfValidator class file
 * 
 * @author Giulio Ganci <giulioganci@gmail.com>
 * @package extensions
 * @link http://www.agenziaentrate.gov.it/wps/content/Nsilib/Nsi/Home/CosaDeviFare/Richiedere/Codice+fiscale+e+tessera+sanitaria/Richiesta+TS_CF/SchedaI/Informazioni+codificazione+pf/ Calcolo codice fiscale
 */

/**
 * CfValidator validates that the attribute value is a valid Italian "Codice Fiscale" (tax code)
 * 
 * @author Giulio Ganci <giulioganci@gmail.com>
 * @package extensions
 */
class CfValidator extends CValidator
{
	/**
	 * Define male gender
	 */
	const GENDER_MALE = 'M';
	
	/**
	 * Define female gender
	 */
	const GENDER_FEMALE = 'F';
	
	/**
	 * Validates the attribute values against model attributes
	 * @var array key value pairs [modelattributes => CfValidator local properties]
	 */
	public $validateWithAttrs;
	
	/**
	 * whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 * @var boolean 
	 */
	public $allowEmpty = false;
	
	/**
	 * Validate CF against the attributes (name,surname,gender etc...)
	 * @var boolean 
	 */
	public $strict = false;
	
	/**
	 * Show all validation errors in the active record attributes
	 * @var boolean 
	 */
	public $showStrictErrors = false;
	
	/**
	 * Validate CF with the gender value
	 * @var string should be M or F 
	 */
	public $gender;
	
	/**
	 * Validate CF with this name value
	 * @var string 
	 */
	public $name;
	
	/**
	 * Validate CF with this surname value
	 * @var string 
	 */	
	public $surname;
	
	/**
	 * Validate CF with this day of birth
	 * @var string 
	 */	
	public $dayOfBirth;
	
	/**
	 * Validate CF with this month of birth
	 * @var string 
	 */	
	public $monthOfBirth;
	
	/**
	 * Validate CF with this year of birth
	 * @var string 
	 */	
	public $yearOfBirth;
	
	/**
	 * Validate CF with this code istat
	 * @link http://www.istat.it/it/archivio/6789 Database
	 * @var string italian Commune code or foreign state
	 */	
	public $codeIstat;
	
	/**
	 * List of error messages
	 * @var array
	 */
	public $formatErrors = array();
	
	/**
	 * Generated CF (only if $strict is true)
	 * @var type string
	 */
	public $cf;
	
	/**
	 * Array of consonants
	 * @var array 
	 */
	protected $consonants = array(
		'B', 'C', 'D', 'F', 'G', 'H', 'J', 'K',
		'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T',
		'V', 'W', 'X', 'Y', 'Z'
	);
	
	/**
	 * Array of vocals
	 * @var array 
	 */
	protected $vocals = array('A', 'E', 'I', 'O', 'U');
	
	/**
	 * Month table
	 * @var array 
	 */
	protected $month = array(
        1  => 'A',  2 => 'B',  3 => 'C',  4 => 'D',  5 => 'E',  
        6  => 'H',  7 => 'L',  8 => 'M',  9 => 'P', 10 => 'R', 
        11 => 'S', 12 => 'T'
	);
	
	/**
	 * List of pairs values
	 * @var array 
	 */
    protected $pairs = array(
        '0' =>  0, '1' =>  1, '2' =>  2, '3' =>  3, '4' =>  4, 
        '5' =>  5, '6' =>  6, '7' =>  7, '8' =>  8, '9' =>  9,
        'A' =>  0, 'B' =>  1, 'C' =>  2, 'D' =>  3, 'E' =>  4, 
        'F' =>  5, 'G' =>  6, 'H' =>  7, 'I' =>  8, 'J' =>  9,
        'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 
        'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
        'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 
        'Z' => 25
    );
	
	/**
	 * List of odd values
	 * @var array 
	 */
    protected $odd = array(  
        '0' =>  1, '1' =>  0, '2' =>  5, '3' =>  7, '4' =>  9,
        '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' =>  1, 'B' =>  0, 'C' =>  5, 'D' =>  7, 'E' =>  9, 
        'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' =>  2, 'L' =>  4, 'M' => 18, 'N' => 20, 'O' => 11, 
        'P' =>  3, 'Q' =>  6, 'R' =>  8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 
        'Z' => 23
    );

	/**
	 * List of check values
	 * @var array 
	 */
    protected $check = array( 
        '0'  => 'A', '1'  => 'B', '2'  => 'C', '3'  => 'D', 
        '4'  => 'E', '5'  => 'F', '6'  => 'G', '7'  => 'H', 
        '8'  => 'I', '9'  => 'J', '10' => 'K', '11' => 'L', 
        '12' => 'M', '13' => 'N', '14' => 'O', '15' => 'P', 
        '16' => 'Q', '17' => 'R', '18' => 'S', '19' => 'T',
        '20' => 'U', '21' => 'V', '22' => 'W', '23' => 'X', 
        '24' => 'Y', '25' => 'Z'
    ); 	

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */	
	protected function validateAttribute($object, $attribute) 
	{
		$value = $object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;	
		
		if($this->strict && $this->validateWithAttrs) {
			foreach($this->validateWithAttrs as $attr => $opt) {
				$this->$opt = $object->$attr;
			}
		}
		
		if(!$this->validateValue($value))
		{
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} non è un codice fiscale valido.');
			if($this->showStrictErrors && count($this->formatErrors)) {
				$message .= implode(', ', $this->formatErrors);
			}
			
			$this->addError($object,$attribute,$message);
		}		
	}
	
	/**
	 * Validates a static value to see if it is a CF
	 * Note that this method does not respect {@link allowEmpty} property.
	 * This method is provided so that you can call it directly without going through the model validation rule mechanism.
	 * @param mixed $value the value to be validated
	 * @return boolean whether the value is a valid CF
	 */	
	public function validateValue($value)
	{
		if(strlen($value) != 16) return false;
		
		$regex_class_month = join('', array_values($this->month));
		$isValid = preg_match('/^([a-z]{3})([a-z]{3})([0-9]{2})(['.$regex_class_month.']{1})([0-9]{2})([a-z]{1}[0-9]{3})([a-z]{1})$/i', $value, $matches);
		if(!$isValid) {
			return false;
		}
		
		list($pattern,$surname,$name,$year,$month,$day,$istat,$check) = $matches;
		
		$csurname = '';
		if($this->strict && ($csurname = $this->getCfString($this->surname, false)) != $surname) {
			$this->formatErrors[] = sprintf('Il cognome "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->surname,$surname,$csurname);
		}
		
		$this->cf = $csurname;
		
		$cname = '';
		if($this->strict && ($cname = $this->getCfString($this->name)) != $name) {
			$this->formatErrors[] = sprintf('Il nome "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->name,$name,$cname);
		}
		
		$this->cf .= $cname;
		
		$cyear = '';
		if($this->strict && ($cyear = substr((string)$this->yearOfBirth, -2, 2)) != $year) {
			$this->formatErrors[] = sprintf('L\'anno di nascita "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->yearOfBirth,$year,$cyear);
		}
		
		$this->cf .= $cyear;
		
		$cmonth = '';
		if($this->strict && ($cmonth = $this->month[(int)$this->monthOfBirth]) != $month) {
			$this->formatErrors[] = sprintf('Il mese di nascita "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->monthOfBirth,$month,$cmonth);
		}
		
		$this->cf .= $cmonth;
		
		$cday = $this->dayOfBirth;
		if($this->strict && $this->gender == self::GENDER_MALE && $day != $this->dayOfBirth) {
			$this->formatErrors[] = sprintf('Il giorno di nascita "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->dayOfBirth,$day,$this->dayOfBirth);
		}
		
		if($this->strict && $this->gender == self::GENDER_FEMALE && $day != ($this->dayOfBirth + 40)) {
			$this->formatErrors[] = sprintf('Il giorno di nascita "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->dayOfBirth,$day,($this->dayOfBirth + 40));
		}
		
		if($this->gender == self::GENDER_FEMALE) {
			$cday = (int)$cday + 40;
		}
		
		$this->cf .= $cday;
		
		if($this->strict && $this->codeIstat != $istat) {
			$this->formatErrors[] = sprintf('Il codice del comune "%s" non è compatibile con il valore "%s" inserito. Il valore corretto dovrebbe essere "%s"',$this->codeIstat,$istat,$this->codeIstat);
			$cistat = $this->codeIstat;
		}
		
		$this->cf .= $this->codeIstat;
		$ccheck = '';
		if($this->strict) {
			$code = str_split($this->cf);
			$sum = 0;
			for($i = 1; $i <= count($code); $i++) {
				$c = $code[$i-1];
				$sum += ($i % 2) ? $this->odd[$c]: $this->pairs[$c];
			}

			if(($ccheck = $this->check[$sum %= 26]) != $check) {
				$this->formatErrors[] = sprintf('Il codice di controllo "%s" non è compatibile per questo codice fiscale. Il valore corretto dovrebbe essere "%s"',$check,$ccheck);			
				$this->cf = strtoupper($this->cf);
			}			
		}
		
		$this->cf .= $ccheck;
		
		if($this->strict && count($this->formatErrors)) {
			$isValid = false;
		}
		
		return $isValid;
	}
	
	/**
	 * Caluclate Name or surname string
	 * @param string $string to parse
	 * @param boolean $name is this the name
	 * @return string parse string
	 */
	protected function getCfString($string, $name = true)
	{
		$i = 0;
		$out = '';
		$string = strtoupper($string);
		
		foreach(str_split($string) as $c) {
			if(in_array($c, $this->consonants)) {
				$out .= $c;
				$i++;
			}
		}		
		
		if($name && strlen($out) > 3) {
			$out = str_split($out);
			$out = $out[0].$out[2].$out[3];
		}
		
		$i = strlen($out);
		
		if($i >= 3) {
			return substr($out, 0,3);
		}
		
		foreach(str_split($string) as $c) {
			if($i == 3) return $out;
			if(in_array($c, $this->vocals)) {
				$out .= $c;
				$i++;
			}
		}
		
        while(strlen($string) < 3) {
            $out .= 'X';
        }    
		
		return $out;
	}
}