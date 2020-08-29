<?php
/**
 * Copyright 2010-2019 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * This file is licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License. A copy of
 * the License is located at
 *
 * http://aws.amazon.com/apache2.0/
 *
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
*/

require 'aws/aws-autoloader.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$sdk = new Aws\Sdk([
    'region'   => 'ap-southeast-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'pir';

$eav = $marshaler->marshalJson('
    {
        ":id": "1d2e3a99-7eb4-4a70-b052-d82337a888e0"
    }
');

#cannot use dynamodb reserved words need to add number behind to change
$params = [
    'TableName' => $tableName,
    'ProjectionExpression' => '#id, payload.SensorID',
    'KeyConditionExpression' =>
        '#id = :id',
    'ExpressionAttributeNames'=> [ '#id' => 'id' ],
    'ExpressionAttributeValues'=> $eav
];

echo "yahoooo---------------------------------------------------------------------------------\n";

try {
    $result = $dynamodb->query($params);

    echo "Query succeeded.\n";

    foreach ($result['Items'] as $i) {
        $pirresult = $marshaler->unmarshalItem($i);
        var_dump($pirresult);
        // print $pirresult["id"];
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}


?>
