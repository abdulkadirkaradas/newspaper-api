<?php

// API method responses
const UNAUTHORIZED_ACCESS = 'Unauthorized access';
const BAD_REQUEST_MSG = "The request could not be understood or was missing required parameters.";
const METHOD_NOT_ALLOWED_MSG = "Method not allowed!";
const INVALID_ID_NO = "Invalid UUID Number!";
const SESSION_EXPIRED = 'Session expired';
const AN_ERROR_OCCURED = 'An unexpected error occured.';
const INCORRECT_CONTENT_TYPE = 'The request header is incorrect. It should be application/json.';
const INCORRECT_IMAGE_UPLOAD_CONTENT_TYPE = 'The request header is incorrect. It should be multipart/form-data.';
const INVALID_ROLE = "You do not have necessary role to access this resource.";

// API function returns
const VALIDATOR_FAILED = "The request could not be understood or was missing required parameters.";
const USER_NOT_FOUND = "User could not be found!";
const NEWS_NOT_FOUND = "News could not be found!";
const NEWS_CATEGORY_NOT_FOUND = "News category could not be found!";
const NOTIFICATION_CREATED = "Notification has been created successfully!";
const NOTIFICATION_CREATION_FAILED = "Notification could not be created!";
const WARNING_CREATED = "Warning message has been created successfully!";
const WARNING_CREATION_FAILED = "Warning message could not be created!";
const NEWS_CREATED = "News has been created successfully!";
const NEWS_CREATION_FAILED = "News could not be created!";
const NEWS_IMAGE_CREATED = "News image has been created successfully!";
const NEWS_IMAGE_CREATION_FAILED = "News image could not be created!";
const BADGE_CREATED = "Badge has been created succesfully!";
const BADGE_CREATION_FAILED = "Badge could not be created!";
const BADGE_IMAGE_CREATED = "Badge image has been created succesfully!";
const BADGE_IMAGE_CREATION_FAILED = "Badge image could not be created!";

// Default user constants
const DEFAULT_USER_ROLE = "Writer";
const DEFAULT_NEWS_PRIORITY = 3;