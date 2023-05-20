<?php
namespace WondPHP;

 use ArrayObject as ArrayObject;
 use Closure as Closure;
 use Illuminate\Container\Container;
 use Illuminate\Contracts\Events\Dispatcher;
 use Illuminate\Contracts\Routing\BindingRegistrar;
 use Illuminate\Contracts\Routing\Registrar as RegistrarContract;
 use Illuminate\Contracts\Support\Arrayable;
 use Illuminate\Contracts\Support\Jsonable;
 use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Support\Renderable;

 use WondPHP\Model as Model;
 use Illuminate\Http\JsonResponse;
//  use Illuminate\Http\Request;
//  use Illuminate\Http\Response;
 use Illuminate\Routing\Events\RouteMatched;
 use Illuminate\Support\Collection;
 use Illuminate\Support\Str;
 use Illuminate\Support\Traits\Macroable;
 use JsonSerializable as JsonSerializable;
//  use Illuminate\Support\ViewErrorBag;
//  use Illuminate\Contracts\Support\MessageProvider;
 //  use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
//  use ReflectionClass;
//  use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
//  use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
 use WondPHP\Http\Response as HttpRespnse;
 use WondPHP\Contracts\Response as ResponseContracts;
//  use Illuminate\Support\MessageBag;
/**
 * Class Response
 *
 */
class Response extends HttpRespnse implements ResponseContracts
{
    public $encodingOptions=0;
    public $callback=null;
    protected $charset;
    // protected $session;
    // public  static $statusCodeTexts;

    /**
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
     */
    private const HTTP_RESPONSE_CACHE_CONTROL_DIRECTIVES = [
        'must_revalidate' => false,
        'no_cache' => false,
        'no_store' => false,
        'no_transform' => false,
        'public' => false,
        'private' => false,
        'proxy_revalidate' => false,
        'max_age' => true,
        's_maxage' => true,
        'immutable' => false,
        'last_modified' => true,
        'etag' => true,
    ];
    // use SingletonTrait;
    public function __construct($status, $headers, $body)
    {
        // $this->session=app('session');
        // self::$statusCodeTexts= parent::$_phrases;
        parent::__construct($status, $headers, $body);
    }

    /**
        * Static version of prepareResponse.
        *
        * @param  \Symfony\Component\HttpFoundation\Request  $request
        * @param  mixed  $response
        * @return \Symfony\Component\HttpFoundation\Response
        */
    public function toResponse($request, $response)
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        if ($response instanceof PsrResponseInterface) {
            // $response = (new HttpFoundationFactory)->createResponse($response); // no
        } elseif ($response instanceof Model && $response->wasRecentlyCreated) {
            $response = $this->JsonResponse($response, 201);
        } elseif (! $response instanceof Response &&
                   ($response instanceof Arrayable ||
                    $response instanceof Jsonable ||
                    $response instanceof ArrayObject ||
                    $response instanceof JsonSerializable ||
                    \is_array($response))) {
            $response = $this->JsonResponse($response);


        } elseif ($response instanceof Renderable) {

            $response = $this->viewResponse($response);


        } elseif (! $response instanceof Response) {
            $response = response($response);
        }

        if ($response->_status === 304) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }

    public function setNotModified(): object
    {
        $this->_status=304;
        $this->withBody('');

        // remove headers that MUST NOT be included with 304 Not Modified responses
        foreach (['Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified'] as $header) {
            $this->withoutHeader($header);
        }

        return $this;
    }
     



    /**
     * Is response informative?
     *
     * @final
     */
    public function isInformational(): bool
    {
        return $this->_status >= 100 && $this->_status < 200;
    }

    public function viewResponse($response)
    {
        echo  $response->render();
        // $this->_status=200;
        // $this->withBody('');
        // $this->withHeader('Content-Type', 'text/html; charset=UTF-8');
        return $this;
    }


    /**
     * Is the response empty?
     *
     * @final
     */
    public function isEmpty(): bool
    {
        return \in_array($this->_status, [204, 304]);
    }
    /**
     * Prepares the Response before it is sent to the client.
     *
     * This method tweaks the Response to ensure that it is
     * compliant with RFC 2616. Most of the changes are based on
     * the Request that is "associated" with this Response.
     *
     * @return $this
     */
    public function prepare(Request $request)
    {
        $headers = $this->_header;

        if ($this->isInformational() || $this->isEmpty()) {
            $this->withBody('');
            $this->withoutHeader('Content-Type');
            $this->withoutHeader('Content-Length');
            // prevent PHP from sending the Content-Type header based on default_mimetype
            ini_set('default_mimetype', '');
        } else {
            // Content-type based on the Request
            if (!isset($headers['Content-Type'])) {
                $format = 'html';//$request->getRequestFormat(null);//
                if (null !== $format && $mimeType = 'text/html') {
                    $this->withHeader('Content-Type', $mimeType);
                }
            }

            // Fix Content-Type
            $charset = $this->charset ?: 'UTF-8';
            if (!isset($headers['Content-Type'])) {
                $this->withHeader('Content-Type', 'text/html; charset='.$charset);
            } elseif (0 === stripos($this->getHeader('Content-Type'), 'text/') && false === stripos($this->getHeader('Content-Type'), 'charset')) {
                // add the charset
                $this->withHeader('Content-Type', $headers['Content-Type'].'; charset='.$charset);
            }

            // Fix Content-Length
            if (isset($headers['Transfer-Encoding'])) {
                $this->withoutHeader('Content-Length');
            }

            if ($request->method()==='HEAD') {
                // cf. RFC2616 14.13
                $length = $headers['Content-Length'] ?? '';
                $this->withBody('');
                if ($length) {
                    $this->withHeader('Content-Length', $length);
                }
            }
        }

        // Fix protocol
        if ('HTTP/1.0' != $_SERVER['SERVER_PROTOCOL']) {
            $this->withProtocolVersion('1.1');
        }

        // Check if we need to send extra expire info headers
        if ('1.0' == $this->_version && false !== strpos($headers['Cache-Control'], 'no-cache')) {
            $this->withHeader('pragma', 'no-cache');
            $this->withHeader('expires', -1);
        }

        $this->ensureIEOverSSLCompatibility($request);

        if ($request->isSecure()) {////////////

            if (isset($this->_header['Set-Cookie'])) {
                foreach ($this->_header['Set-Cookie'] as $cookie) {//todo
                    // header('Set-Cookie: '.$cookie, false, $this->_status);
                // $cookie->setSecureDefault(true);
                }
            }
        }

        return $this;
    }


    /**
     * Checks if we need to remove Cache-Control for SSL encrypted downloads when using IE < 9.
     *
     * @see http://support.microsoft.com/kb/323308
     *
     * @final
     */
    protected function ensureIEOverSSLCompatibility(Request $request): void
    {
        if (false !== stripos($this->_header['Content-Disposition'] ?? '', 'attachment') && 1 == preg_match('/MSIE (.*?);/i', $_SERVER['HTTP_USER_AGENT'] ?? '', $match) && true === $request->isSecure()) {
            if ((int) preg_replace('/(MSIE )(.*?);/', '$2', $match[0]) < 9) {
                $this->withoutHeader('Cache-Control');
            }
        }
    }
    public function JsonResponse($data, $status = 200)
    {
        // $this->original = $data;

        if ($data instanceof Jsonable) {
            $this->data = $data->toJson($this->encodingOptions);//todo
        } elseif ($data instanceof JsonSerializable) {
            $this->data = json_encode($data->jsonSerialize(), $this->encodingOptions);
        } elseif ($data instanceof Arrayable) {
            $this->data = json_encode($data->toArray(), $this->encodingOptions);//todo
        } else {
            $this->data = json_encode($data, $this->encodingOptions);
        }

        if (! $this->hasValidJson(json_last_error())) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return $this->update();
    }

    /**
         * Updates the content and headers according to the JSON data and callback.
         *
         * @return $this
         */
    protected function update()
    {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->withHeader('Content-Type', 'text/javascript');

            return $this->withBody(sprintf('/**/%s(%s);', $this->callback, $this->data));
        }

        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if (!isset($this->_header['Content-Type']) || 'text/javascript' === $this->getHeader('Content-Type')) {
            $this->withHeader('Content-Type', 'application/json');
        }

        return $this->withBody($this->data);
    }
    /**
     * Determine if an error occurred during JSON encoding.
     *
     * @param  int  $jsonError
     * @return bool
     */
    protected function hasValidJson($jsonError)
    {
        if ($jsonError === JSON_ERROR_NONE) {
            return true;
        }

        return $this->hasEncodingOption(JSON_PARTIAL_OUTPUT_ON_ERROR) &&
                    \in_array($jsonError, [
                        JSON_ERROR_RECURSION,
                        JSON_ERROR_INF_OR_NAN,
                        JSON_ERROR_UNSUPPORTED_TYPE,
                    ]);
    }

    /**
     * {@inheritdoc}//todo
     */
    public function setEncodingOptions($options)
    {
        $this->encodingOptions = (int) $options;

        return $this->setData($this->getData());
    }


    /**
     * Sets the response's cache headers (validation and/or expiration).
     *
     * Available options are: must_revalidate, no_cache, no_store, no_transform, public, private, proxy_revalidate, max_age, s_maxage, immutable, last_modified and etag.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     *
     * @final
     */
    public function setCache(array $options): object
    {
        if ($diff = array_diff(array_keys($options), array_keys(self::HTTP_RESPONSE_CACHE_CONTROL_DIRECTIVES))) {
            throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', $diff)));
        }

        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }

        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }

        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }

        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }

        foreach (self::HTTP_RESPONSE_CACHE_CONTROL_DIRECTIVES as $directive => $hasValue) {

            if (!$hasValue && isset($options[$directive])) {
                if ($options[$directive]) {

                    // $this->header(str_replace('_', '-', $directive),'ddddd');
                } else {
                    // $this->withoutHeader(str_replace('_', '-', $directive));
                }
            }
        }

        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }

        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }

        return $this;
    }


    /**
     * Marks the response as "public".
     *
     * It makes the response eligible for serving other clients.
     *
     * @return $this
     *
     * @final
     */
    public function setPublic(): object
    {
        $this->header('Cache-Control','public');
        $this->withoutHeader('private');

        return $this;
    }

    /**
     * Marks the response as "private".
     *
     * It makes the response ineligible for serving other clients.
     *
     * @return $this
     *
     * @final
     */
    public function setPrivate(): object
    {
        $this->withoutHeader('public');

        $this->header('Cache-Control','private');
 

        return $this;
    }

    /**
     * Sets the Last-Modified HTTP header with a DateTime instance.
     *
     * Passing null as value will remove the header.
     *
     * @return $this
     *
     * @final
     */
    public function setLastModified(\DateTimeInterface $date = null): object
    {
        if (null === $date) {
            $this->withoutHeader('Last-Modified');

            return $this;
        }

        if ($date instanceof \DateTime) {
            $date = \DateTimeImmutable::createFromMutable($date);
        }

        $date = $date->setTimezone(new \DateTimeZone('UTC'));
        $this->header('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');

        return $this;
    }

    /**
     * Sets the number of seconds after which the response should no longer be considered fresh.
     *
     * This methods sets the Cache-Control max-age directive.
     *
     * @return $this
     *
     * @final
     */
    public function setMaxAge(int $value): object
    {
        $this->header('Cache-Control', 'max-age='. $value);

        return $this;
    }


    /**
     * Determines if the Response validators (ETag, Last-Modified) match
     * a conditional value specified in the Request.
     *
     * If the Response is not modified, it sets the status code to 304 and
     * removes the actual content by calling the setNotModified() method.
     *
     * @return bool true if the Response validators match the Request, false otherwise
     *
     * @final
     */
    public function isNotModified(Request $request): bool
    {
        if (!$request->isMethodCacheable()) {
            return false;
        }

        $notModified = false;
        $lastModified = $this->_header['Last-Modified'] ?? '';
        $modifiedSince = $request->header('If-Modified-Since');

        if ($etags = $request->getETags()) {
            $notModified = \in_array($this->getEtag(), $etags) || \in_array('*', $etags);
        }

        if ($modifiedSince && $lastModified) {
            $notModified = strtotime($modifiedSince) >= strtotime($lastModified) && (!$etags || $notModified);
        }

        if ($notModified) {
            $this->setNotModified();
        }

        return $notModified;
    }


    /**
     * Sets the ETag value.
     *
     * @param string|null $etag The ETag unique identifier or null to remove the header
     * @param bool        $weak Whether you want a weak ETag or not
     *
     * @return $this
     *
     * @final
     */
    public function setEtag(string $etag = null, bool $weak = false): object
    {
        if (null === $etag) {
            // $this->headers->remove('Etag');
            $this->withoutHeader('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"'.$etag.'"';
            }

            $this->header('ETag', (true === $weak ? 'W/' : '').$etag);
        }

        return $this;
    }

    /**
     * Determine if a JSON encoding option is set.
     *
     * @param  int  $option
     * @return bool
     */
    public function hasEncodingOption($option)
    {
        return (bool) ($this->encodingOptions & $option);
    }

    // /**
    //  * Flash an array of input to the session.
    //  *
    //  * @param  array|null  $input
    //  * @return $this
    //  */
    // public function withInput(array $input = null)
    // {
    //     $this->session->flashInput($this->removeFilesFromInput(
    //         ! is_null($input) ? $input : $this->request->all()
    //     ));

    //     return $this;
    // }

    // /**
    //  * Remove all uploaded files form the given input array.
    //  *
    //  * @param  array  $input
    //  * @return array
    //  */
    // protected function removeFilesFromInput(array $input)
    // {
    //     foreach ($input as $key => $value) {
    //         if (is_array($value)) {
    //             $input[$key] = $this->removeFilesFromInput($value);
    //         }

    //        /* if ($value instanceof SymfonyUploadedFile) {
    //             unset($input[$key]);
    //         }*/
    //     }

    //     return $input;
    // }
    // /**
    //  * Flash a container of errors to the session.
    //  *
    //  * @param  \Illuminate\Contracts\Support\MessageProvider|array|string  $provider
    //  * @param  string  $key
    //  * @return $this
    //  */
    // public function withErrors($provider, $key = 'default')
    // {
    //     $value = $this->parseErrors($provider);

    //     $errors = $this->session->get('errors', new ViewErrorBag);

    //     if (! $errors instanceof ViewErrorBag) {
    //         $errors = new ViewErrorBag;
    //     }

    //     $this->session->flash(
    //         'errors', $errors->put($key, $value)
    //     );

    //     return $this;
    // }

    // /**
    //  * Parse the given errors into an appropriate value.
    //  *
    //  * @param  \Illuminate\Contracts\Support\MessageProvider|array|string  $provider
    //  * @return \Illuminate\Support\MessageBag
    //  */
    // protected function parseErrors($provider)
    // {
    //     if ($provider instanceof MessageProvider) {
    //         return $provider->getMessageBag();
    //     }

    //     return new MessageBag((array) $provider);
    // }
}
