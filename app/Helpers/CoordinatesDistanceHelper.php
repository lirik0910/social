<?php


namespace App\Helpers;


class CoordinatesDistanceHelper
{
    const EARTH_RADIUS = 6378.137; // in kilometers

    /**
     * Calculate distance between points of coordinates
     *
     * @param float $lat1 Point from latitude
     * @param float $lng1 Point from longitude
     * @param float $lat2 Point to latitude
     * @param float $lng2 Point to longitude
     * @return int Distance between points in km
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2)
    {
        // convert from degrees to radians
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $distance = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng1 - $lng2)) * self::EARTH_RADIUS;

        return (int) $distance;
    }

    /**
     * Return array of min/max coordinates for searching radius
     *
     * @param float $lat Point latitude
     * @param float $lng Point longitude
     * @param int   $max_radius Boundary max radius in kilometers
     * @return array
     */
    public static function calculateBoundaryCoordinates(float $lat, float $lng, int $max_radius)
    {
        $r = $max_radius / self::EARTH_RADIUS;

        $max_lat = $lat + rad2deg($r);
        $min_lat = $lat - rad2deg($r);

        $max_lng = $lng + rad2deg($r / cos(deg2rad($lat)));
        $min_lng = $lng - rad2deg($r / cos(deg2rad($lat)));

        return [
            'min_lat' => $min_lat,
            'max_lat' => $max_lat,
            'min_lng' => $min_lng,
            'max_lng' => $max_lng
        ];
    }
}
