<?php


namespace App\Helpers;


/*
 * Responsible for validating & sanitizing inputs
 */
class Validator
{
    /**
     * Receives allowed input field names, and returns validated data to the caller,
     * then validates based on predefined rules inside this method.
     * If validation fails it halts execution and returns json response to the user
     * containing errors info.
     *
     * @param array $fields
     * @return array
     */
    public static function validate(array $fields): array
    {

        // Get the POST JSON contents
        $post = file_get_contents('php://input');
        $post = json_decode($post, true);

        // Fields allowed max & minimum size
        $maxMin = [
            'to' => [
                'max' => 12,
                'min' => 10,
            ],
            'message' => [
                'max' => 255,
                'min' => 1,
            ],
        ];

        // Will hold validated input values
        $values = [];

        // Empty fields are not allowed
        $errors = self::checkForEmpty($fields, $post);

        // If no errors found,then sanitize and validate
        if (count($errors) < 1) {
            $values = self::sanitize($fields, $post);
            $errors = self::checkForEmpty($fields, $values);
        }

        // Validate fields
        if (count($errors) < 1) {
            foreach ($fields as $field) {
                switch ($field) {

                    case 'to':
                        if (!is_numeric($values[$field])) {
                            $errors[] = 'To field must be numeric.';
                            break;
                        }
                        if (
                            strlen((string) $values[$field]) > $maxMin[$field]['max']
                            || strlen((string) $values[$field]) < $maxMin[$field]['min']
                        ) {
                            $errors[] = 'To field must be between '
                                . $maxMin[$field]['min'] 
                                . '-' 
                                . $maxMin[$field]['max'] .' digits long.';
                        }
                        break;

                    case 'message':
                        if (
                            strlen($values[$field]) > $maxMin[$field]['max']
                            || strlen($values[$field]) < $maxMin[$field]['min']
                        ) {
                            $errors[] = 'Message characters length must be between '
                                . $maxMin[$field]['min'] 
                                . '-' 
                                .$maxMin[$field]['max'];
                        }
                        break;
                }
            }
        }

        // If no error found, then return validated values to the caller.
        if (count($errors) > 0) {
            self::sendErrorResponse($errors);
            exit();
        }

        return $values;
    }

    /**
     * Response a the errors
     *
     * @param array $errors
     */
    public static function sendErrorResponse(array $errors): void
    {
        // Send error response
        Response::send(
            [
                'success' => false,
                'message' => 'Validation error! Please check your fields.',
                'data' => [
                    'errors' => $errors
                ],
            ],
            200
        );
    }

    /**
     * Checks if any field is empty & returns an index array of field names
     *
     * @param array $fields
     * @param array $data
     * @return array
     */
    public static function checkForEmpty(array $fields, array $data): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst($field) . ' field is invalid or empty';
            }
        }
        return $errors;
    }

    /**
     * Sanitize a value
     *
     * @param array $fields
     * @param array $data
     * @return array
     */
    public static function sanitize(array $fields, array $data): array
    {
        $values = [];
        foreach ($fields as $field) {
            switch ($field) {
                case 'to':
                    $values[$field] = self::filter($data[$field], FILTER_SANITIZE_NUMBER_INT);
                    break;

                case 'message':
                    $values[$field] = self::filter($data[$field]);
                    break;
            }
        }
        return $values;
    }

    /**
     * Filter value
     *
     * @param mixed $value
     * @param int $sanitizeValue
     * @return mixed
     */
    public static function filter(mixed $value, int $sanitizeValue = FILTER_UNSAFE_RAW): mixed
    {
        $value = trim($value);
        $value = htmlspecialchars($value);
        return filter_var($value, $sanitizeValue);
    }

}
