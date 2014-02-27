Yii-Codice-Fiscale-Validator
============================

Codice Fiscale (tax code or CF) is the italian code  used to uniquely identify citizens for tax and administrative purpose

http://it.wikipedia.org/wiki/Codice_fiscale

#### Todo
- Client Validation

### Install
Just copy the __CfValidator.php__ in your project extensions directory ([YourProject]/protected/extensions)

### Usage in active record
```php
class Citizen extends CActiveRecord
{
	[...]
	public function rules()
	{
		return array(
			[...]
			array('tax_code', 'ext.CfValidator',
				'strict' => true,
				'validateWithAttrs' => array( // key value pairs [modelattributes => CfValidator local properties]
					'nome' => 'name',
					'cognome' => 'surname',
					'genere' => 'gender',
					'giorno_nascita' => 'dayOfBirth',
					'mese_nascita' => 'monthOfBirth',
					'anno_nascita' => 'yearOfBirth',
					'comune_nascita' => 'codeIstat',
				)
			)

		);
	}
	[...]
}
```

### Static usage
```php
Yii::import('ext.CfValidator');
$cf = new CfValidator();
$cf->strict = true;
$cf->gender = CfValidator::GENDER_MALE;
$cf->name = 'Mario';
$cf->surname = 'Rossi';
$cf->dayOfBirth = '01';
$cf->monthOfBirth = '01';
$cf->yearOfBirth = '1980';
$cf->codeIstat = 'H501';

if(!$cf->validateValue("MRARSS80A01H501T") {
	echo "Error TAX CODE shoud be: " . $cf->cf
}
```